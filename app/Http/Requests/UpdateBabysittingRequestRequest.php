<?php

namespace App\Http\Requests;

use App\Models\BabysittingRequestStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBabysittingRequestRequest extends FormRequest
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
        $babysittingRequest = $this->route('babysittingRequest');
        $now = Carbon::now();

        // Different rules based on status
        if ($babysittingRequest->status === BabysittingRequestStatus::findByName('pending')) {
            $minTime = $now->copy()->addHours(6)->format('Y-m-d H:i:s');
            $maxTime = $now->copy()->addDays(30)->format('Y-m-d H:i:s');
        } else {
            // For confirmed/assigned bookings, can only change with at least 24 hours notice
            $minTime = $now->copy()->addHours(24)->format('Y-m-d H:i:s');
            $maxTime = $now->copy()->addDays(30)->format('Y-m-d H:i:s');
        }

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'start_datetime' => [
                'sometimes',
                'date',
                'after_or_equal:' . $minTime,
                'before_or_equal:' . $maxTime,
            ],
            'end_datetime' => [
                'sometimes',
                'date',
                'after:start_datetime',
            ],
            'message' => 'sometimes|nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $babysittingRequest = $this->route('babysittingRequest');

        if ($babysittingRequest->status === BabysittingRequestStatus::findByName('pending')) {
            return [
                'start_datetime.after_or_equal' => 'Booking must be at least 6 hours in advance.',
            ];
        } else {
            return [
                'start_datetime.after_or_equal' => 'Changes must be made at least 24 hours in advance.',
            ];
        }
    }
}
