<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSharedTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\SharedTemplate;
use App\Models\Template;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SharedTemplateController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function import(ImportSharedTemplateRequest $request): JsonResponse
    {
        $sharedTemplate = SharedTemplate::query()
            ->where('code', $request->validated('code'))
            ->where('expires_at', '>', now())
            ->first();

        if (! $sharedTemplate) {
            throw ValidationException::withMessages([
                'code' => 'O código informado é inválido.',
            ]);
        }

        $template = DB::transaction(function () use ($request, $sharedTemplate): Template {
            $template = $request->user()->templates()->create([
                'name' => $this->importedTemplateNameFor($request->user(), $sharedTemplate->snapshot['name']),
            ]);

            foreach ($sharedTemplate->snapshot['sectors'] as $snapshotSector) {
                $sector = $template->sectors()->create([
                    'name' => $snapshotSector['name'],
                    'order' => $snapshotSector['order'],
                ]);

                foreach ($snapshotSector['products'] as $snapshotProduct) {
                    $sector->products()->create([
                        'name' => $snapshotProduct['name'],
                    ]);
                }
            }

            return $template;
        });

        $template->load('sectors.products');

        return (new TemplateResource($template))
            ->additional(ApiResponse::resourceMeta('Template importado com sucesso.'))
            ->response()
            ->setStatusCode(201);
    }

    private function importedTemplateNameFor(User $user, string $name): string
    {
        if (! $user->templates()->where('name', $name)->exists()) {
            return $name;
        }

        $attempt = 1;

        do {
            $suffix = $attempt === 1 ? ' (Imported)' : " (Imported {$attempt})";
            $candidate = Str::substr($name, 0, 50 - strlen($suffix)).$suffix;
            $attempt++;
        } while ($user->templates()->where('name', $candidate)->exists());

        return $candidate;
    }
}
