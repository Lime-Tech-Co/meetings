<?php

namespace Modules\V1\Meetings\Controllers\Actions;

use App\Http\Actions\Action;
use Illuminate\Http\Request;

class GetAvailableEmployeesTime extends Action
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function validate(): bool|array
    {
        if (($errors = parent::validate()) !== true) {
            return $errors;
        }

        return true;
    }

    public function execute()
    {
        dd('maintenat vous etes ici :)');
    }

    protected function rules(): array
    {
        return [];
    }
}
