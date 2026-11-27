<?php

declare(strict_types=1);

namespace App\Concerns\Erp;

use App\Models\FinancialAccount;
use App\Models\PaymentMethod;
use Closure;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use InertiaUI\Modal\Modal;

trait CashFlowResponses
{
    private function ensurePayable(FinancialAccount $account): void
    {
        abort_unless($account->type === FinancialAccount::TYPE_PAYABLE, 404);
    }

    private function write(Closure $write): void
    {
        try {
            $write();
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['record' => $exception->getMessage()]);
        }
    }

    /** @return Collection<int, PaymentMethod> */
    private function paymentMethods(): Collection
    {
        return PaymentMethod::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /** @param array<string, mixed> $props */
    private function modal(Request $request, string $page, array $props): Modal|Response
    {
        return $request->user()->canAny(['cash-entry.index', 'financial-account.index'])
            ? Inertia::modal($page, [...$props, 'modal' => true])->baseRoute('erp.cash-flow.index')
            : Inertia::render($page, $props);
    }

    private function redirect(Request $request, string $kind, string $message): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'success', 'message' => $message]);

        $canReadCash = $request->user()->can('cash-entry.index');
        $canReadPayables = $request->user()->can('financial-account.index');

        if (! $canReadCash && ! $canReadPayables) {
            return to_route('dashboard');
        }

        $base = route('erp.cash-flow.index');
        $previous = $request->header(Modal::HEADER_BASE_URL) ?? url()->previous();

        if (strtok($previous, '?') === $base) {
            return redirect()->to($previous);
        }

        return to_route('erp.cash-flow.index', ['tab' => match (true) {
            $kind === 'payable' && $canReadPayables, ! $canReadCash => 'payables',
            $kind === 'receipt' => 'receipts',
            default => 'expenses',
        }]);
    }
}
