<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagUpdateRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $locales = array_keys(config('superadmin.moduleLanguages'));
        return [
            'id'     => ['required', Rule::exists('user_tags', 'id')->where(function (Builder $q) {
                $q->where('status', 3);
                $q->whereNull('deleted_at');
            })],
            'value'  => 'required|string',
            'locale' => ['required', 'in:' . implode(',', $locales)],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
