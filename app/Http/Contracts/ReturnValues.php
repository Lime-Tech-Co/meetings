<?php

namespace App\Http\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ReturnValues
{
    /**
     * Generate a success response
     *
     * @param string|null                                                 $msg  Message to showing to the client
     * @param array|AnonymousResourceCollection|LengthAwarePaginator|null $data Array of data to be return to the user
     *
     * @return JsonResponse
     */
    public function successfulReturn(
        string $msg = null,
        array|AnonymousResourceCollection|LengthAwarePaginator $data = null
    ): JsonResponse {
        $finalData = [];

        $msg and $finalData['message'] = $msg;
        $data and $finalData['data'] = $data;

        return $this->stateReturn(
            200,
            $finalData
        );
    }

    /**
     * Generate a failed response
     *
     * @param integer      $code      HTTP status code related to the error
     * @param string|null  $msg       Message about the errors
     * @param array|null   $errors    Array contain information about errors occurred
     * @param integer|null $errorCode Error code for exploring detail of this error
     *
     * @return JsonResponse
     */
    public function failedReturn(
        int $code,
        string $msg = null,
        array $errors = null,
        int $errorCode = null
    ): JsonResponse {
        $finalData = [];

        $errors and $finalData['errors'] = $errors;
        $code and $finalData['code'] = $errorCode;
        $msg and $finalData['message'] = $msg;

        return $this->stateReturn($code, $finalData);
    }

    /**
     * @param integer $code HTTP status code
     * @param array   $data general data to return
     *
     * @return JsonResponse
     */
    protected function stateReturn(int $code, array $data): JsonResponse
    {
        return Response()->json(
            $data,
            $code
        );
    }
}
