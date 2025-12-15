<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\PaymentConditionRequest;
use App\Http\Requests\QueryRequest;
use App\Models\PaymentCondition;
use App\Services\Management\PaymentConditionService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class PaymentConditionController extends Controller
{
    public function __construct(private PaymentConditionService $paymentConditionService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'code', 'name', 'days_until_due', 'installments', 'is_active', 'created_at', 'updated_at'];
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $paymentConditions = $this->paymentConditionService->getPaginatedPaymentConditions(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        Log::info('Payment Condition: accessed index page', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/payment-conditions/index', [
            'paymentConditions' => $paymentConditions,
        ]);
    }

    public function create()
    {
        Log::info('Payment Condition: access creation form', ['action_user_id' => Auth::id()]);

        return Inertia::render('erp/payment-conditions/create');
    }

    public function edit(PaymentCondition $paymentCondition)
    {
        Log::info('Payment Condition: access editing form', ['payment_condition_id' => $paymentCondition->id, 'action_user_id' => Auth::id()]);

        return Inertia::render('erp/payment-conditions/edit', [
            'paymentCondition' => $paymentCondition,
        ]);
    }

    public function update(PaymentConditionRequest $request, PaymentCondition $paymentCondition)
    {
        $userId = Auth::id();

        $validated = $request->validated();

        try {
            Log::info('Payment Condition: updating', ['payment_condition_id' => $paymentCondition->id, 'action_user_id' => Auth::id()]);

            $paymentCondition->update($validated);

            return to_route('erp.payment-conditions.index')->with('success', 'Payment condition updated successfully.');
        } catch (Exception $e) {
            Log::error('Payment Condition: Error updating payment condition.', [
                'action_user_id' => $userId,
                'payment_condition_id' => $paymentCondition->id,
                'error_message' => $e->getMessage(),
            ]);

            return to_route('erp.payment-conditions.index')->with('error', 'An error occurred while updating the payment condition.');
        }
    }

    public function destroy(PaymentCondition $paymentCondition)
    {
        $userId = Auth::id();

        try {
            Log::info('Payment Condition: deleting', ['payment_condition_id' => $paymentCondition->id, 'action_user_id' => Auth::id()]);

            $paymentCondition->delete();

            return to_route('erp.payment-conditions.index')->with('success', 'Payment condition deleted successfully.');
        } catch (Exception $e) {
            Log::error('Payment Condition: Error deleting payment condition.', [
                'action_user_id' => $userId,
                'payment_condition_id' => $paymentCondition->id,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while deleting the payment condition.');
        }
    }
}
