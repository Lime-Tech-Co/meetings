<?php

namespace App\Http\Actions;

use Illuminate\Http\Request;

class BaseAction
{
    protected $request;
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

        return $model->{$getterMethod}($getterParameter);
    }
}
