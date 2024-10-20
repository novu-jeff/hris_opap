<?php

namespace App\Http\Requests\Admin\Hris;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class saveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(int $id = null): array
    {
        return [
            'records.employee_account.email' => [
                'required',
                'email',
                // Rule::unique('employee_account', 'email')
                //     ->ignore($id), 
            ],
            'records.employee_personal.firstname' => 'required|string|max:255',
            'records.employee_personal.lastname' => 'required|string|max:255',
            
            'records.employee_children.*.firstname' => 'required|string|max:255',
            'records.employee_children.*.middlename' => 'nullable|string|max:255',
            'records.employee_children.*.lastname' => 'required|string|max:255',
            'records.employee_children.*.birthdate' => 'required|date',

            'records.employee_education.*.level' => 'required|string',
            'records.employee_education.*.school_name' => 'required|string|max:255',
            'records.employee_education.*.course' => 'required|string|max:255',
            'records.employee_education.*.from_year' => 'required|date',
            'records.employee_education.*.to_year' => 'required|date',

            'records.employee_employment_history.*.position' => 'required|string|max:255',
            'records.employee_employment_history.*.department' => 'required|string|max:255',
            'records.employee_employment_history.*.company_name' => 'required|string|max:255',
            'records.employee_employment_history.*.monthly_salary' => 'required|numeric|min:0',
            'records.employee_employment_history.*.employment_status' => 'required|string',
            'records.employee_employment_history.*.isGovernment' => 'required|string',
            'records.employee_employment_history.*.from_year' => 'required|date',
            'records.employee_employment_history.*.to_year' => 'required|date|after_or_equal:records.employee_employment_history.*.from_year',

        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */

}
