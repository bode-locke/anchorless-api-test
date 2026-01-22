<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use App\Enums\FileCategory;
use Illuminate\Validation\Rules\Enum;

/**
 * Class UploadFileRequest
 *
 * @package App\Http\Requests
 */
class UploadFileRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::types(['pdf', 'png', 'jpg', 'jpeg'])
                    ->max(4096),
            ],
            'category' => ['required', new Enum(FileCategory::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded item must be a file.',
            'file.mimes' => 'Allowed file types: PDF, PNG, JPG.',
            'file.max' => 'The file is too large (max 4MB).',
            'category.required' => 'Please choose a category.',
            'category.enum' => 'The selected category is invalid.',
        ];
    }
}
