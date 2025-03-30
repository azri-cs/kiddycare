<?php

namespace App\Http\Requests;

use App\Models\BabysittingRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update status', $this->route('babysittingRequest'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $babysittingRequest = $this->route('babysittingRequest');
        $currentStatus = $babysittingRequest->status;
        $allowedTransitions = $this->getAllowedStatusTransitions($currentStatus);

        return [
            'status' => [
                'required',
                Rule::in($allowedTransitions),
            ],
        ];
    }

    /**
     * Get the allowed status transitions based on the current status
     *
     * @param BabysittingRequestStatus $currentStatus
     * @return array<string>
     */
    protected function getAllowedStatusTransitions(BabysittingRequestStatus $currentStatus): array
    {
        return match($currentStatus->name) {
            'pending' => BabysittingRequestStatus::whereIn('name', ['confirmed', 'assigned', 'cancelled'])
                ->pluck('name')
                ->toArray(),
            'confirmed' => BabysittingRequestStatus::whereIn('name', ['assigned', 'cancelled'])
                ->pluck('name')
                ->toArray(),
            'assigned' => BabysittingRequestStatus::whereIn('name', ['completed', 'cancelled', 'no_show'])
                ->pluck('name')
                ->toArray(),
            'completed', 'cancelled', 'no_show' => [],
            default => []
        };
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The selected status transition is not allowed.',
        ];
    }
}
