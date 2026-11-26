<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;

final class PaymentMethodController extends Controller
{
    use ErpResourceResponses;

    private const string PAGE = 'erp/settings/payment-methods';

    private const string INDEX_ROUTE = 'erp.settings.payment-methods.index';

    private const string INDEX_PERMISSION = 'payment-method.index';

    public function index(DataTableRequest $request): Response
    {
        $query = PaymentMethod::query();
        $archived = $request->validated('trashed') === 'only';

        if ($archived) {
            $query->onlyTrashed();
        }

        return $this->renderResourceIndex(
            $request, self::PAGE, $query->toBase(),
            ['name' => 'text', 'fee_type' => 'text', 'fee_rate' => 'number', 'is_active' => 'number'], ['is_active'], archived: $archived,
        );
    }

    public function create(Request $request): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/create', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'New payment method',
            'values' => ['fee_type' => PaymentMethod::FEE_FIXED, 'fee_rate' => 0, 'is_active' => true],
        ]);
    }

    public function store(PaymentMethodRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => PaymentMethod::query()->create($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Payment method created.'));
    }

    public function show(Request $request, PaymentMethod $paymentMethod): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $paymentMethod->name,
            'details' => [
                'Name' => $paymentMethod->name,
                'Archive status' => $paymentMethod->trashed() ? 'Archived' : 'Current',
                'Fee type' => ucfirst($paymentMethod->fee_type),
                'Fee rate' => ['feeRate' => $paymentMethod->fee_rate, 'feeType' => $paymentMethod->fee_type],
                'Status' => $paymentMethod->is_active ? 'Active' : 'Inactive',
            ],
        ]);
    }

    public function edit(Request $request, PaymentMethod $paymentMethod): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/edit', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Edit payment method',
            'id' => $paymentMethod->getKey(),
            'values' => $paymentMethod->only(['name', 'fee_type', 'fee_rate', 'is_active']),
        ]);
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->writeResource(fn () => PaymentMethod::query()->whereKey($paymentMethod->getKey())->lockForUpdate()->firstOrFail()->update($request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Payment method updated.'));
    }

    public function archive(Request $request, PaymentMethod $paymentMethod): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Archive '.$paymentMethod->name,
            'id' => $paymentMethod->getKey(),
        ]);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->writeResource(fn () => PaymentMethod::query()->whereKey($paymentMethod->getKey())->lockForUpdate()->firstOrFail()->delete());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Payment method archived.'));
    }

    public function restore(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->writeResource(fn () => PaymentMethod::onlyTrashed()->whereKey($paymentMethod->getKey())->lockForUpdate()->firstOrFail()->restore());

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Payment method restored.'));
    }
}
