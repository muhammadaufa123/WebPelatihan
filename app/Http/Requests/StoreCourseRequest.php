<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'trainer']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'path_trailer' => 'required|string|max:255',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'course_mode_id' => 'required|exists:course_modes,id',
            'course_level_id' => 'required|exists:course_levels,id',
            'about' => 'required|string|max:1000',
            'course_keypoints' => 'nullable|array',
            'course_keypoints.*' => 'nullable|string|max:255',
        ];
    }
}