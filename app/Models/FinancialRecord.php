<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'type',
        'amount',
        'record_date',
        'attachment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'record_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
