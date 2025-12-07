<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $commercial_proposal_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $subtotal_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CommercialProposal $commercialProposal
 * @property-read \App\Models\Product $product
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereCommercialProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereSubtotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCommercialProposal whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ItemCommercialProposal extends Model
{
    protected $fillable = [
        'commercial_proposal_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal_price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal_price' => 'decimal:2',
        ];
    }

    public function commercialProposal()
    {
        return $this->belongsTo(CommercialProposal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
