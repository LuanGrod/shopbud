<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReorderSectorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $expectedIds = $this->template
                    ->sectors()
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->all();

                $submittedIds = collect($this->integerIds())
                    ->sort()
                    ->values()
                    ->all();

                if ($submittedIds !== $expectedIds) {
                    $validator->errors()->add('ids', 'The ids must include every sector from this template and no other sectors.');
                }
            },
        ];
    }

    /**
     * @return list<int>
     */
    public function integerIds(): array
    {
        return collect($this->validated('ids'))
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }
}
