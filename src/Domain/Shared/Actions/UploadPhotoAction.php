<?php

namespace Domain\Shared\Actions;

use Illuminate\Support\Facades\Storage;
use Infra\Shared\Foundations\Action;

class UploadPhotoAction extends Action
{
    public function execute($file)
    {
        $url = Storage::disk('s3')->put('ckeditor', $file);
        $fullUrl = Storage::disk('s3')->url($url);

        return $fullUrl;

    }
}
