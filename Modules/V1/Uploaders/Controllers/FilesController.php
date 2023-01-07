<?php

namespace Modules\V1\Uploaders\Controllers;

use App\Http\Controllers\Controller;
use Modules\V1\Uploaders\Controllers\Actions\UploadFile;

class FilesController extends Controller
{
    /**
     * @api               {post} api/v1/files Upload A File
     * @apiVersion        1.0.0
     * @apiName           UploadFile
     * @apiGroup          Files
     * @apiPermission     None
     *
     * @apiDescription    upload new file.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *        "message": "File uploaded successfully.",
     *        "data": {
     *            "id": 1540da7e-4e8b-47f2-a242-f898dce437be,
     *            "file_name": "062803c69fa24888534d3274768489e6.txt"
     *        }
     *     }
     *
     * @apiError          cannot upload file
     *
     * @apiErrorExample   Error-Response:
     *     HTTP/1.1 500 invalid credentials
     *     {
     *       "errors": {
     *          "message": "Upload file failed",
     *       },
     *       "code": 11xxx
     *     }
     *
     * @apiErrorExample   Error-Response:
     *     HTTP/1.1 400 invalid credentials
     *     {
     *       "errors": {
     *          "file": "this field is required.",
     *       },
     *       "code": 11xxx
     *     }
     */
    public function uploadNewFile(UploadFile $action): \Illuminate\Http\JsonResponse
    {
        if (($errors = $action->validate()) !== true) {
            return $this->returner->failedReturn(
                400,
                null,
                $errors,
                11001
            );
        }

        $result = $action->execute();

        if (!empty($result['error'])) {
            return $this->returner->failedReturn(
                400,
                null,
                $result['error'],
                11001
            );
        }

        return $this->returner->successfulReturn(null, $result);
    }
}
