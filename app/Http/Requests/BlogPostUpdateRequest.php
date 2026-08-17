<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:blog_posts,slug'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string'],
            'featured_image' => ['nullable', 'string'],
            'author_id' => ['required'],
            'published_at' => ['nullable'],
        ];
    }
}
