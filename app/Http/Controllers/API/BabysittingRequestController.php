<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBabysittingRequestRequest;
use App\Http\Resources\BabysittingRequestResource;
use App\Models\BabysittingRequest;
use App\Models\BabysittingRequestStatus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BabysittingRequestController extends Controller
{
    public function store(StoreBabysittingRequestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $babysittingRequestData = collect($validated)->except(['care_recipients'])->toArray();
        $babysittingRequest = new BabysittingRequest($babysittingRequestData);

        if ($request->user()) {
            $babysittingRequest->user_id = $request->user()->id;
        }

        $babysittingRequest->status = BabysittingRequestStatus::findByName('pending');

        if (isset($validated['care_recipients']) && is_array($validated['care_recipients'])) {
            $babysittingRequest->care_recipient_count = count($validated['care_recipients']);
        } else {
            $babysittingRequest->care_recipient_count = 0;
        }

        $babysittingRequest->save();

        // Handle care recipients
        if (isset($validated['care_recipients']) && is_array($validated['care_recipients'])) {
            foreach ($validated['care_recipients'] as $recipientData) {
                $babysittingRequest->addCareRecipient($recipientData);
            }
        }

        return (new BabysittingRequestResource($babysittingRequest->fresh(['careRecipients'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
