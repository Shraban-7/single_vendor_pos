<?php

namespace App\Http\Requests;

use App\Enums\HomeSectionSource;
use App\Enums\HomeSectionType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $homeSection = $this->route('home_section');
        $homeSectionId = is_object($homeSection) ? $homeSection->id : $homeSection;

        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:home_sections,slug,' . $homeSectionId],
            'type' => ['required', 'string', 'in:' . implode(',', array_column(HomeSectionType::cases(), 'value'))],
            'source' => ['nullable', 'string', 'in:' . implode(',', array_column(HomeSectionSource::cases(), 'value'))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'show_countdown' => ['nullable', 'boolean'],
            'view_all_url' => ['nullable', 'string', 'max:255'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
