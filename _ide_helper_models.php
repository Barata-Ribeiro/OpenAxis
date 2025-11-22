<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * @property int $id
     * @property string $addressable_type
     * @property int $addressable_id
     * @property string $type
     * @property string|null $label A label to identify the address, e.g., Home, Office
     * @property string $street
     * @property string $number
     * @property string $complement
     * @property string $neighborhood
     * @property string $city
     * @property string $state
     * @property string $postal_code
     * @property string $country
     * @property int $is_primary Indicates if this is the primary address for the entity
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read Model|\Eloquent $addressable
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddressableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddressableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereComplement($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereIsPrimary($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereLabel($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereNeighborhood($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address wherePostalCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereState($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereStreet($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class Address extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $bank_account_id
     * @property string $type
     * @property numeric $amount
     * @property string $movement_date
     * @property string $description
     * @property string|null $reference_number
     * @property int|null $destination_account_id
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\BankAccount $bankAccount
     * @property-read \App\Models\BankAccount|null $destinationAccount
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereBankAccountId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereDestinationAccountId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereMovementDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereReferenceNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceMovement whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class BalanceMovement extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string $type
     * @property string $bank_name
     * @property string $bank_agency
     * @property string $bank_account_number
     * @property numeric $initial_balance
     * @property numeric $current_balance
     * @property bool $is_active
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAccountNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankAgency($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereBankName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereCurrentBalance($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereInitialBalance($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereIsActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BankAccount whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class BankAccount extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property string|null $phone_number
     * @property string $identification Social Security Number/Employer Identification Number of the client. If Brazilian, follow the CPF format or CNPJ for companies.
     * @property string $client_type Type of client: individual or company. In Brazil would be Pessoa Física or Pessoa Jurídica.
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
     * @property-read int|null $addresses_count
     * @property-read bool|null $addresses_exists
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereClientType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIdentification($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhoneNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class Client extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $code
     * @property int $client_id
     * @property int $vendor_id
     * @property \Illuminate\Support\Carbon $proposal_date
     * @property \Illuminate\Support\Carbon|null $valid_until
     * @property string $status
     * @property numeric $total_value
     * @property string|null $notes
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Partner $client
     * @property-read \App\Models\User $user
     * @property-read \App\Models\Vendor $vendor
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereProposalDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereTotalValue($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereValidUntil($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereVendorId($value)
     *
     * @mixin \Eloquent
     */
    class CommercialProposal extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $commercial_proposal_id
     * @property int $product_id
     * @property int $quantity
     * @property numeric $unit_price
     * @property numeric $subtotal_price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\CommercialProposal $commercialProposal
     * @property-read \App\Models\Product $product
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereCommercialProposalId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereQuantity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereSubtotalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereUnitPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class ItemCommercialProposal extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $purchase_order_id
     * @property int $product_id
     * @property int $quantity
     * @property numeric $unit_price
     * @property numeric $subtotal_price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Product $product
     * @property-read \App\Models\PurchaseOrder $purchaseOrder
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder wherePurchaseOrderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereQuantity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereSubtotalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereUnitPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemPurchaseOrder whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class ItemPurchaseOrder extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $sales_order_id
     * @property int $product_id
     * @property int $quantity
     * @property numeric $unit_price
     * @property numeric $subtotal_price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Product $product
     * @property-read \App\Models\SalesOrder $salesOrder
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereQuantity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereSalesOrderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereSubtotalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereUnitPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemSalesOrder whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class ItemSalesOrder extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $type Defines whether the partner is a client, supplier, or both.
     * @property string $name
     * @property string $email
     * @property string|null $phone_number
     * @property string $identification Social Security Number/Employer Identification Number of the client. If Brazilian, follow the CPF format or CNPJ for companies.
     * @property bool $is_active Indicates whether the supplier is currently active.
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
     * @property-read int|null $addresses_count
     * @property-read bool|null $addresses_exists
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereIdentification($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereIsActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner wherePhoneNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class Partner extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $description
     * @property int $supplier_id
     * @property numeric $amount
     * @property \Illuminate\Support\Carbon $issue_date
     * @property \Illuminate\Support\Carbon $due_date
     * @property \Illuminate\Support\Carbon|null $payment_date
     * @property string $status
     * @property string $payment_method
     * @property int $bank_account_id
     * @property string|null $reference_number
     * @property string|null $notes
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\BankAccount $bankAccount
     * @property-read \App\Models\Partner $supplier
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereBankAccountId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereDueDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereIssueDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable wherePaymentDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable wherePaymentMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereReferenceNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereSupplierId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payable whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class Payable extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $code
     * @property string $name
     * @property string|null $description
     * @property string $slug
     * @property numeric $cost_price Cost price of the product
     * @property numeric $selling_price Selling price of the product
     * @property int $current_stock Current stock level of the product
     * @property int $minimum_stock Minimum stock level of the product
     * @property numeric $comission Commission percentage for the product
     * @property bool $is_active
     * @property int $product_category_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
     * @property-read int|null $audits_count
     * @property-read bool|null $audits_exists
     * @property-read \App\Models\ProductCategory|null $category
     * @property-read mixed $cover_image
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read bool|null $media_exists
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereComission($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCostPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCurrentStock($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMinimumStock($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellingPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class Product extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property string $slug
     * @property bool $is_active
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
     * @property-read int|null $audits_count
     * @property-read bool|null $audits_exists
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
     * @property-read int|null $products_count
     * @property-read bool|null $products_exists
     *
     * @method static \Database\Factories\ProductCategoryFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereIsActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class ProductCategory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $order_number
     * @property \Illuminate\Support\Carbon $order_date
     * @property \Illuminate\Support\Carbon|null $forecast_date
     * @property string $status
     * @property numeric $total_cost Total cost of the purchase order
     * @property string|null $notes
     * @property int $supplier_id
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Partner $supplier
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereForecastDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereOrderDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereOrderNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereSupplierId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereTotalCost($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseOrder whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class PurchaseOrder extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $description
     * @property int $client_id
     * @property numeric $amount
     * @property \Illuminate\Support\Carbon $issue_date
     * @property \Illuminate\Support\Carbon $due_date
     * @property \Illuminate\Support\Carbon|null $received_date
     * @property string $status
     * @property string $payment_method
     * @property int $bank_account_id
     * @property string|null $reference_number
     * @property string|null $notes
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\BankAccount $bankAccount
     * @property-read \App\Models\Partner $client
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereBankAccountId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereClientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereDueDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereIssueDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable wherePaymentMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereReceivedDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereReferenceNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Receivable whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class Receivable extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $client_id
     * @property int $vendor_id
     * @property string $order_number
     * @property \Illuminate\Support\Carbon $order_date
     * @property \Illuminate\Support\Carbon|null $delivery_date
     * @property string $status
     * @property numeric $product_cost Cost of the products in the sales order
     * @property numeric $delivery_cost Cost of delivery for the sales order
     * @property numeric $discount_cost Discount applied to the sales order
     * @property numeric $total_cost Total cost of the sales order
     * @property numeric $product_value Total value of the products in the sales order
     * @property string $payment_method
     * @property string|null $notes
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Partner $client
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereClientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDeliveryCost($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDeliveryDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereDiscountCost($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereOrderDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereOrderNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder wherePaymentMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereProductCost($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereProductValue($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereTotalCost($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesOrder whereVendorId($value)
     *
     * @mixin \Eloquent
     */
    class SalesOrder extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $sales_orders_id
     * @property string $tracking_number
     * @property string $carrier
     * @property \Illuminate\Support\Carbon $shipped_date
     * @property string $status
     * @property string|null $notes
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\SalesOrder $salesOrder
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereCarrier($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereSalesOrdersId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereShippedDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereTrackingNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippedOrder whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class ShippedOrder extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $product_id
     * @property int $user_id
     * @property string $movement_type
     * @property int $quantity
     * @property string|null $reason
     * @property string|null $reference
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Product $product
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereMovementType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereQuantity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereReason($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|StockMovement whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class StockMovement extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $two_factor_secret
     * @property string|null $two_factor_recovery_codes
     * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
     * @property-read int|null $addresses_count
     * @property-read bool|null $addresses_exists
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
     * @property-read int|null $audits_count
     * @property-read bool|null $audits_exists
     * @property-read mixed $avatar
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
     * @property-read int|null $media_count
     * @property-read bool|null $media_exists
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read bool|null $notifications_exists
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read bool|null $permissions_exists
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
     * @property-read int|null $roles_count
     * @property-read bool|null $roles_exists
     *
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \OwenIt\Auditing\Contracts\Auditable, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $first_name
     * @property string $last_name
     * @property \Illuminate\Support\Carbon|null $date_of_birth
     * @property string|null $phone_number
     * @property numeric $commission_rate Commission rate as a percentage (e.g., 15.00 for 15%)
     * @property bool $is_active Indicates whether the vendor is currently active.
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCommissionRate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereDateOfBirth($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereFirstName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereIsActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereLastName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePhoneNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class Vendor extends \Eloquent {}
}
