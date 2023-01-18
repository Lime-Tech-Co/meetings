<?php

namespace App\Http\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseAction
{
    protected Request $request;
    protected $errors = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function errors(): array
    {
        return (array)$this->errors;
    }

    protected function returner($model)
    {
        $getterMethod = $this->request->input('paginated', false) == true ? 'paginate' : 'get';
        $getterParameter =
            $this->request->input('paginated', false) == true ? $this->request->input('per_page', 15) : ['*'];

        /**
         * Collections will be paginated if you get() method
         * is already called the below logic would handle
         * the response.
         */
        if (
            ($this->request->input('paginated', false) == true && $model instanceof Collection) ||
            ($this->request->input('paginated', false) == true && $model instanceof AnonymousResourceCollection)
        ) {
            return new LengthAwarePaginator(
                $model->forPage($this->request->input('page', 1), $getterParameter),
                $model->count(),
                $getterParameter,
                $this->request->input('page', 1),
            );
        }

        if (
            ($this->request->input('paginated', false) == false && $model instanceof Collection) ||
            ($this->request->input('paginated', false) == false && $model instanceof AnonymousResourceCollection)
        ) {
            return $model;
        }

        return $model->{$getterMethod}($getterParameter);
    }
}
