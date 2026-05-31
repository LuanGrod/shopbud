<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per-page', 10);
        $perPage = min(max($perPage, 1), 100);

        $templates = $request->user()
            ->templates()
            ->withSearch($request)
            ->withSort($request)
            ->paginate($perPage);

        return TemplateResource::collection($templates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemplateRequest $request)
    {
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

        return new TemplateResource($template);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemplateRequest $request, Template $template)
    {
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
}
