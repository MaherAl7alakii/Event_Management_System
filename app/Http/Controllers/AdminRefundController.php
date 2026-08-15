<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\AdminRefundRequest;
use App\Models\Payment;
use App\Services\RefundService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class AdminRefundController extends Controller
{
    public function __construct(
        private readonly RefundService $refunds,
    ) {
    }


    public function refund(AdminRefundRequest $request): JsonResponse
    {
        $data = $request->validated();
        $payment = Payment::findOrFail($data['payment_id']);

        try {
            $this->refunds->refund($payment, (float) $data['amount'], $data['reason'] ?? 'admin_manual_refund');
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Refund issued successfully.']);
    }
}
