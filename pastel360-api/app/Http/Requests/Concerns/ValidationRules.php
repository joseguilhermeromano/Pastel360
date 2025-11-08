<?php

namespace App\Http\Requests\Concerns;

trait ValidationRules
{
    protected function stringMax255(): string
    {
        return 'sometimes|string|max:255';
    }

    protected function requiredStringMax255(): string
    {
        return 'required|string|max:255';
    }
}
