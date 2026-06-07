<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingSessionRequest;
use App\Http\Resources\ShoppingSessionResource;
use App\Models\ShoppingSession;
use App\Models\Template;
use App\Models\User;
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
        return ShoppingSession::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }
}
