<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCareRecipientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('babysittingRequest'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    $dob = Carbon::parse($value);
                    $now = Carbon::now();
                    $ageInMonths = $now->diffInMonths($dob);

                    if ($ageInMonths < 1) {
                        $fail('The child must be at least 1 month old.');
                    }

                    if ($ageInMonths > 155) { // 12 years and 11 months
                        $fail('The child must be at most 12 years and 11 months old.');
                    }
                },
            ],
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Check if the babysitting request already has the maximum number of care recipients
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $babysittingRequest = $this->route('babysittingRequest');

            if ($babysittingRequest->careRecipients()->count() >= 4) {
                $validator->errors()->add('babysitting_request_id', 'Maximum of 4 children allowed per request.');
            }
        });
    }
}
