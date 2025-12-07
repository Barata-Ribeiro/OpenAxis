<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
class CommercialProposal extends Model
{
    protected $fillable = [
        'code',
        'client_id',
        'vendor_id',
        'proposal_date',
        'valid_until',
        'status',
        'total_value',
        'notes',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proposal_date' => 'date',
            'valid_until' => 'date',
            'total_value' => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Partner::class, 'client_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
