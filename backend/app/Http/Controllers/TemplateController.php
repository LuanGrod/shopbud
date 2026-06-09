<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\SharedTemplate;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Template::class);

        $perPage = (int) $request->get('per-page', 10);
        $perPage = min(max($perPage, 1), 100);

        $templates = $request->user()
            ->templates()
            ->withSearch($request)
            ->when(
                $request->has('sort'),
                fn ($query) => $query->withSort($request),
                fn ($query) => $query->latest('updated_at')
            )->paginate($perPage);

        return TemplateResource::collection($templates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemplateRequest $request)
    {
        $this->authorize('create', Template::class);

        $template = $request->user()->templates()->create(
            $request->validated()
        );

        return response()->json($template, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Template $template)
    {
        $this->authorize('view', $template);

        $template->load('sectors.products');

        return new TemplateResource($template);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemplateRequest $request, Template $template)
    {
        $this->authorize('update', $template);

        $template->update($request->validated());

        return new TemplateResource($template);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        return response()->noContent();
    }

    public function share(Template $template): JsonResponse
    {
        $this->authorize('view', $template);

        $template->load('sectors.products');

        $sharedTemplate = SharedTemplate::query()->create([
            'template_id' => $template->id,
            'code' => $this->uniqueShareCode(),
            'snapshot' => $this->snapshotFrom($template),
            'expires_at' => now()->addDay(),
        ]);

        return response()->json([
            'data' => [
                'code' => $sharedTemplate->code,
                'expires_at' => $sharedTemplate->expires_at->toJSON(),
                'template' => [
                    'name' => $template->name,
                    'sectors_count' => $template->sectors->count(),
                    'products_count' => $template->sectors->sum(fn ($sector): int => $sector->products->count()),
                ],
            ],
        ], 201);
    }

    private function uniqueShareCode(): string
    {
        do {
            $code = Str::random(40);
        } while (SharedTemplate::query()->where('code', $code)->exists());

        return $code;
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
}
