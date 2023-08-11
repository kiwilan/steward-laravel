<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Enums\MediaTypeEnum;

trait HasAttachment
{
    /**
     * Save attachment.
     *
     * @param  string[]  $fields
     */
    public function saveAttachments(array $fields, Model $model, Request $request, string $field_name = null, MediaTypeEnum $type = MediaTypeEnum::media)
    {
        $filesystem_disk = storage_path('app/public/'.$type->name);
        $directory_path = '/'.$type->name.'/';

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                $ext = $file->getClientOriginalExtension();
                $file_name = Str::slug($field_name).'-'.uniqid().'.'.$ext;
                $file_path = "{$filesystem_disk}/{$file_name}";

                File::put($file_path, $file->getContent());

                $model->{$field} = "{$directory_path}{$file_name}";
            }
        }

        return $model;
    }
}
