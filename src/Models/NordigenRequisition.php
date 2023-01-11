<?php

namespace Hypnodev\LaravelNordigen\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NordigenRequisition extends Model
{
    protected $fillable = [
        'user_id', 'institution_id', 'agreement', 'reference'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
