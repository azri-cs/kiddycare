<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BabysittingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusModel = $this->whenLoaded('statusRelation')
            ?? $this->statusRelation;

        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'message' => $this->message,
            'status' => $this->status,
            'status_label' => $statusModel->label ?? null,
            'status_color' => $statusModel->color ?? null,
            'care_recipients' => CareRecipientResource::collection($this->whenLoaded('careRecipients')),
            'user' => new UserResource($this->whenLoaded('user')),
            'handler' => new UserResource($this->whenLoaded('handler')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
