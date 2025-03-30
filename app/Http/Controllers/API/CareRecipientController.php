<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCareRecipientRequest;
use App\Http\Requests\UpdateCareRecipientRequest;
use App\Http\Resources\CareRecipientResource;
use App\Models\BabysittingRequest;
use App\Models\CareRecipient;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CareRecipientController extends Controller
{
    public function show(CareRecipient $careRecipient): CareRecipientResource
    {
        $this->authorize('view', $careRecipient);

        return new CareRecipientResource($careRecipient);
    }

    public function store(StoreCareRecipientRequest $request, BabysittingRequest $babysittingRequest): JsonResponse
    {
        $this->authorize('update', $babysittingRequest);

        try {
            $careRecipient = $babysittingRequest->addCareRecipient($request->validated());

            return (new CareRecipientResource($careRecipient))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(UpdateCareRecipientRequest $request, CareRecipient $careRecipient): CareRecipientResource
    {
        $this->authorize('update', $careRecipient);

        try {
            $careRecipient->update($request->validated());

            return new CareRecipientResource($careRecipient);
        } catch (\Exception $e) {
            abort(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function destroy(CareRecipient $careRecipient): JsonResponse
    {
        $this->authorize('delete', $careRecipient);

        $careRecipient->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
