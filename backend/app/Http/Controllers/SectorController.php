<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderSectorsRequest;
use App\Http\Requests\StoreSectorRequest;
use App\Http\Requests\UpdateSectorRequest;
use App\Http\Resources\SectorResource;
use App\Models\Sector;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
    public function index(Template $template): AnonymousResourceCollection
    {
        $this->authorize('view', $template);

        return SectorResource::collection($template->sectors()->get());
    }

    public function store(StoreSectorRequest $request, Template $template): JsonResponse
    {
        $sector = $template->sectors()->create([
            ...$request->validated(),
            'order' => ((int) $template->sectors()->max('order')) + 1,
        ]);

        return (new SectorResource($sector))->response()->setStatusCode(201);
    }

    public function update(UpdateSectorRequest $request, Template $template, Sector $sector): SectorResource
    {
        $sector->update($request->validated());

        return new SectorResource($sector);
    }

    public function destroy(Template $template, Sector $sector): Response
    {
        $this->authorize('update', $template);
        abort_unless($sector->template_id === $template->id, 403);

        $sector->delete();

        return response()->noContent();
    }

    public function reorder(ReorderSectorsRequest $request, Template $template): AnonymousResourceCollection
    {
        DB::transaction(function () use ($request, $template): void {
            foreach ($request->integerIds() as $index => $sectorId) {
                $template->sectors()
                    ->whereKey($sectorId)
                    ->update(['order' => $index + 1]);
            }
        });

        return SectorResource::collection($template->sectors()->get());
    }
}
