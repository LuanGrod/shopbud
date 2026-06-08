<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishShoppingSessionRequest;
use App\Http\Requests\StoreShoppingSessionRequest;
use App\Http\Resources\ShoppingSessionResource;
use App\Models\PurchaseHistory;
use App\Models\ShoppingItem;
use App\Models\ShoppingSession;
use App\Models\Template;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShoppingSessionController extends Controller
{
    public function current(Request $request): ShoppingSessionResource|Response
    {
        $activeSession = $this->activeSessionFor($request->user()->id);

        if ($activeSession === null) {
            return response()->noContent();
        }

        return new ShoppingSessionResource($activeSession);
    }

    public function store(StoreShoppingSessionRequest $request): JsonResponse
    {
        $template = Template::query()
            ->with('sectors.products')
            ->findOrFail($request->integer('template_id'));

        $this->authorize('view', $template);

        if ($template->sectors->isEmpty()) {
            throw ValidationException::withMessages([
                'template_id' => 'The selected template must have at least one sector.',
            ]);
        }

        [$shoppingSession, $wasCreated] = DB::transaction(function () use ($request, $template): array {
            User::query()
                ->whereKey($request->user()->id)
                ->lockForUpdate()
                ->first();

            $activeSession = $this->activeSessionFor($request->user()->id);

            if ($activeSession !== null) {
                return [$activeSession, false];
            }

            return [
                ShoppingSession::query()->create([
                    'user_id' => $request->user()->id,
                    'template_id' => $template->id,
                    'status' => 'active',
                    'snapshot' => $this->snapshotFrom($template),
                    'expires_at' => now()->addDay(),
                ]),
                true,
            ];
        });

        return (new ShoppingSessionResource($shoppingSession))
            ->response()
            ->setStatusCode($wasCreated ? 201 : 200);
    }

    public function cancel(ShoppingSession $shoppingSession): ShoppingSessionResource
    {
        $this->authorize('cancel', $shoppingSession);

        $shoppingSession->update([
            'status' => 'cancelled',
        ]);

        return new ShoppingSessionResource($shoppingSession);
    }

    public function finish(FinishShoppingSessionRequest $request, ShoppingSession $shoppingSession): ShoppingSessionResource
    {
        $this->cancelExpiredActiveSessionsFor($request->user()->id);
        $shoppingSession->refresh();

        $this->authorize('finish', $shoppingSession);

        $items = $request->validated('items', []);

        $shoppingSession = DB::transaction(function () use ($items, $shoppingSession): ShoppingSession {
            $shoppingSession = ShoppingSession::query()
                ->whereKey($shoppingSession->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($shoppingSession->status !== 'active' || $shoppingSession->expires_at->isPast()) {
                throw new AuthorizationException;
            }

            $summary = $this->finishSummaryFor($shoppingSession, $items);

            foreach ($summary['sectors'] as $sector) {
                foreach ($sector['items'] as $item) {
                    ShoppingItem::query()->create([
                        'session_id' => $shoppingSession->id,
                        'sector_name' => $sector['name'],
                        'product_name' => $item['product_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'extra' => $item['extra'],
                    ]);
                }
            }

            $shoppingSession->update([
                'status' => 'finished',
            ]);

            PurchaseHistory::query()->create([
                'user_id' => $shoppingSession->user_id,
                'template_name' => $shoppingSession->snapshot['name'],
                'finished_at' => now(),
                'total' => $summary['total'],
                'sectors_summary' => $summary['sectors'],
            ]);

            $shoppingSession->summary = $summary;

            return $shoppingSession;
        });

        return new ShoppingSessionResource($shoppingSession);
    }

    /**
     * @return array{name: string, sectors: array<int, array{id: int, name: string, order: int, products: array<int, array{id: int, name: string}>}>}
     */
    private function snapshotFrom(Template $template): array
    {
        return [
            'name' => $template->name,
            'sectors' => $template->sectors->map(fn ($sector): array => [
                'id' => $sector->id,
                'name' => $sector->name,
                'order' => $sector->order,
                'products' => $sector->products->map(fn ($product): array => [
                    'id' => $product->id,
                    'name' => $product->name,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    private function activeSessionFor(int $userId): ?ShoppingSession
    {
        $this->cancelExpiredActiveSessionsFor($userId);

        return ShoppingSession::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    private function cancelExpiredActiveSessionsFor(int $userId): void
    {
        ShoppingSession::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'cancelled']);
    }

    /**
     * @param  array<int, array{sector_snapshot_id: int, product_name: string, price: int|float|string, quantity: int, extra: bool}>  $items
     * @return array{sectors: array<int, array{sector_snapshot_id: int, name: string, subtotal: string, items: array<int, array{product_name: string, price: string, quantity: int, extra: bool, subtotal: string}>}>, total: string}
     */
    private function finishSummaryFor(ShoppingSession $shoppingSession, array $items): array
    {
        $snapshotSectors = collect($shoppingSession->snapshot['sectors']);
        $sectorsById = $snapshotSectors->keyBy('id');

        foreach ($items as $index => $item) {
            if (! $sectorsById->has($item['sector_snapshot_id'])) {
                throw ValidationException::withMessages([
                    "items.{$index}.sector_snapshot_id" => 'The selected sector must exist in the shopping session snapshot.',
                ]);
            }
        }

        $itemsBySectorId = collect($items)->groupBy('sector_snapshot_id');
        $totalCents = 0;

        $sectors = $snapshotSectors->map(function (array $sector) use ($itemsBySectorId, &$totalCents): array {
            $sectorSubtotalCents = 0;
            $sectorItems = $itemsBySectorId
                ->get($sector['id'], collect())
                ->map(function (array $item) use (&$sectorSubtotalCents): array {
                    $itemSubtotalCents = $this->priceToCents($item['price']) * $item['quantity'];
                    $sectorSubtotalCents += $itemSubtotalCents;

                    return [
                        'product_name' => $item['product_name'],
                        'price' => $this->formatCents($this->priceToCents($item['price'])),
                        'quantity' => $item['quantity'],
                        'extra' => $item['extra'],
                        'subtotal' => $this->formatCents($itemSubtotalCents),
                    ];
                })
                ->values()
                ->all();

            $totalCents += $sectorSubtotalCents;

            return [
                'sector_snapshot_id' => $sector['id'],
                'name' => $sector['name'],
                'subtotal' => $this->formatCents($sectorSubtotalCents),
                'items' => $sectorItems,
            ];
        })->values()->all();

        return [
            'sectors' => $sectors,
            'total' => $this->formatCents($totalCents),
        ];
    }

    private function priceToCents(int|float|string $price): int
    {
        return (int) round(((float) $price) * 100);
    }

    private function formatCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
