<?php

namespace App\Http\Actions;

use Illuminate\Support\Facades\Validator;

abstract class Action extends BaseAction
{
    protected $validatorRules = null;

    public function validate(): bool|array
    {
        $this->validatorRules = Validator::make(
            $this->request->all(),
            $this->rules()
        );

        $validatorPagination = Validator::make(
            $this->request->all(),
            [
                'paginated' => 'boolean',
                'per_page'  => 'integer|min:1',
            ]
        );

        if ($this->validatorRules->passes() && $validatorPagination->passes()) {
            return true;
        }

        $errors = array_merge(
            $this->validatorRules->getMessageBag()->messages(),
            $validatorPagination->getMessageBag()->messages()
        );

        $this->errors = array_merge($this->errors(), $errors);

        return $errors;
    }

    abstract protected function rules();

    abstract protected function execute();
}
