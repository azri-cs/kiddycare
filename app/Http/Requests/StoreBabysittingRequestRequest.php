<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBabysittingRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can make a booking
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $now = Carbon::now();
        $minTime = $now->copy()->addHours(6)->format('Y-m-d H:i:s');
        $maxTime = $now->copy()->addDays(30)->format('Y-m-d H:i:s');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'start_datetime' => [
                'required',
                'date',
                'after_or_equal:' . $minTime,
                'before_or_equal:' . $maxTime,
            ],
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime',
            ],
            'message' => 'nullable|string|max:1000',

            // Care recipients info
            'care_recipients' => 'required|array|min:1|max:4',
            'care_recipients.*.name' => 'required|string|max:255',
            'care_recipients.*.date_of_birth' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    $dob = Carbon::parse($value);
                    $now = Carbon::now();
                    $ageInMonths = $dob->diffInMonths($now, false);

                    if ($ageInMonths < 1) {
                        $fail('The child must be at least 1 month old.');
                    }

                    if ($ageInMonths > 155) { // 12 years and 11 months
                        $fail('The child must be at most 12 years and 11 months old.');
                    }
                },
            ],
            'care_recipients.*.remarks' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_datetime.after_or_equal' => 'Booking must be at least 6 hours in advance.',
            'start_datetime.before_or_equal' => 'Booking must be within the next 30 days.',
            'care_recipients.required' => 'At least one child is required for babysitting.',
            'care_recipients.max' => 'Maximum 4 children allowed per booking.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->is('api/*') || $this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
