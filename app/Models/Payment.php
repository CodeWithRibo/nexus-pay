<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'amount_paid',
        'user_id',
        'student_balance_id',
        'transaction_id',
        'status',
        'payment_channel',
        'payment_context',
        'pay_all',
        'use_overpayment',
        'gateway_reference_id',
        'gateway_payment_id',
        'gateway_status',
        'gateway_method',
        'gateway_qr_code',
        'gateway_response',
        'failed_reason',
    ];

    protected function casts(): array
    {
        return [
            'pay_all' => 'boolean',
            'use_overpayment' => 'boolean',
            'gateway_response' => 'array',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentBalance() : BelongsTo
    {
        return $this->belongsTo(StudentBalance::class);
    }
}
