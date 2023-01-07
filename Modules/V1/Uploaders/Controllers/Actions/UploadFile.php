<?php

namespace Modules\V1\Uploaders\Controllers\Actions;

use App\Http\Actions\Action;
use Illuminate\Http\Request;
use Modules\V1\Uploaders\Models\File;
use Modules\V1\Uploaders\Models\Constants\FileMaxSize;
use Modules\V1\Uploaders\Models\Constants\FileMimeTypes;
use Modules\V1\Uploaders\Controllers\Helpers\FileInfoGetter;
use Modules\V1\Uploaders\Services\Uploaders\Local\UploadToLocal;

class UploadFile extends Action
{
    protected UploadToLocal $uploaderService;

    public function __construct(Request $request, UploadToLocal $uploaderService)
    {
        parent::__construct($request);
        $this->uploaderService = $uploaderService;
    }

    public function validate(): bool|array
    {
        if (($errors = parent::validate()) !== true) {
            return $errors;
        }

        return true;
    }

    public function execute(): mixed
    {
        try {
            $file = $this->request->file('file');
            $fileInfo = FileInfoGetter::extractFileInfos($file);
            $filePath = $this->uploaderService->doUpload($file);

            $file = File::create([
                'filename'  => $fileInfo['filename'],
                'extension' => $fileInfo['extension'],
                'mimetype'  => $fileInfo['mimeType'],
                'path'      => $filePath,
                'size'      => $fileInfo['size'],
            ]);

            return [
                'message' => __('files.file_uploaded'),
                'data'    => [
                    'id'       => $file->id,
                    'filename' => $file->filename,
                ],
            ];
        } catch (\Exception $ex) {
            \Log::error('Cannot upload file :' . $ex->getMessage());

            return [
                'error' => [
                    'message' => __('files.file_upload_failed'),
                ],
            ];
        }
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'max:' . FileMaxSize::MAX_SIZE_IN_KB->value,
                'mimes:' . FileMimeTypes::ALLOWED_MIME_TYPES->value,
            ],
        ];
    }
}
