<?php

namespace App\Http\Requests\Concerns;

use App\Helpers\ValidationHelper;

trait UsesFriendlyValidation
{
    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ValidationHelper::attributes(ValidationHelper::fieldsFromRules($this->rules()));
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ValidationHelper::messages(
            ValidationHelper::fieldsFromRules($this->rules()),
            $this->friendlyValidationOverrides()
        );
    }

    /**
     * @return array<string, string>
     */
    protected function friendlyValidationOverrides(): array
    {
        return [];
    }
}
