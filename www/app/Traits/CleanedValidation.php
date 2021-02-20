<?php
/**
 * Cleans and validates the request inputs
 *
 * @author drixlerangelo
 *
 * @since 20 February 2021
 */
namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait CleanedValidation
{
    /**
     * Parameters to be validated
     *
     * @var array
     */
    protected $selectedParams = [];

    /**
     * Extract the user inputs from the selected parameters
     *
     * @return array
     */
    protected function prepareInputs()
    {
        $selectedInputs = request()->only($this->selectedParams);
        return $selectedInputs;
    }

    /**
     * Retrieve the validation rules to be applied to parameters
     *
     * @return array
     */
    protected function fetchRules()
    {
        $validationRules = [];

        foreach ($this->selectedParams as $param) {
            $validationRules[$param] = config(sprintf('validation.%s.rules', $param));
        }

        return $validationRules;
    }

    /**
     * Perform the validation
     *
     * @param array $inputs
     *
     * @param array $rules
     *
     * @return array
     */
    protected function conductTest($inputs, $rules)
    {
        return Validator::make($inputs, $rules)->validate();
    }
}
