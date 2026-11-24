<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp;

use App\Concerns\CashFlowResponses;
use App\Http\Requests\Erp\CashFlowEntryRequest;
use App\Models\CashEntry;
use App\Models\FinancialAccount;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class PayablePaymentController extends Controller
{
    use CashFlowResponses;

    public function create(Request $request, FinancialAccount $financialAccount): Modal|Response
    {
        $this->ensurePayable($financialAccount);
        $outstanding = $financialAccount->outstandingCents();
        abort_unless($outstanding > 0, 409, 'This account has no outstanding balance.');

        return $this->modal($request, [
            'title' => 'Confirm payment', 'kind' => 'payment', 'mode' => 'create', 'id' => $financialAccount->id,
            'values' => ['occurred_at' => now()->format('Y-m-d\TH:i'), 'expected_amount_cents' => $outstanding],
            'choices' => ['payment_method_id' => $this->paymentMethods()],
            'details' => ['Description' => $financialAccount->description, 'Outstanding' => ['cents' => $outstanding]],
        ]);
    }

    public function store(CashFlowEntryRequest $request, FinancialAccount $financialAccount): RedirectResponse
    {
        $data = $request->validated();

        $this->write(function () use ($financialAccount, $data, $request): void {
            DB::transaction(function () use ($financialAccount, $data, $request): void {
                $account = FinancialAccount::query()->lockForUpdate()->findOrFail($financialAccount->id);
                $this->ensurePayable($account);
                $outstanding = $account->cancelled_at === null ? $account->amount_cents - $account->lockAndGetSettledCents() : 0;

                if ($outstanding <= 0) {
                    throw new DomainException('This account has no outstanding balance.');
                }

                if ($outstanding !== (int) $data['expected_amount_cents']) {
                    throw new DomainException('The outstanding balance changed. Open the confirmation again to review the new amount.');
                }

                $account->cashEntries()->create([
                    'payment_method_id' => $data['payment_method_id'],
                    'notes' => $data['notes'] ?? null,
                    'type' => CashEntry::TYPE_EXPENSE, 'description' => $account->description,
                    'amount_cents' => $outstanding, 'created_by_id' => $request->user()->id,
                    'occurred_at' => Carbon::createFromFormat('Y-m-d\TH:i', $data['occurred_at'])->startOfMinute(),
                ]);
            }, attempts: 3);
        });

        return $this->redirect($request, 'payable');
    }
}
