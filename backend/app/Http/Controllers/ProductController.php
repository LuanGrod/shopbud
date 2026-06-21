<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Sector;
use App\Models\Template;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Template $template, Sector $sector): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Product::class, $sector]);

        return ProductResource::collection($sector->products()->get())
            ->additional(ApiResponse::resourceMeta());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, Template $template, Sector $sector): JsonResponse
    {
        $this->authorize('create', [Product::class, $sector]);

        $product = $sector->products()->create($request->validated());

        return (new ProductResource($product))
            ->additional(ApiResponse::resourceMeta('Produto criado com sucesso.'))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Template $template, Sector $sector, Product $product): ProductResource
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return (new ProductResource($product))
            ->additional(ApiResponse::resourceMeta('Produto atualizado com sucesso.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template, Sector $sector, Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return ApiResponse::success(message: 'Produto removido com sucesso.');
    }
}
