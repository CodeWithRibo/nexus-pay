<?php

namespace App\Services\Kiosk;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReceiptService
{
    public function __construct()
    {
    }

    public static  function generateRefNo()
    {
        $suffix = 'NP';
        $date = Carbon::now()->format('ymd');
        $random = Str::upper(Str::random(5));

        $referenceNo = "{$suffix}-{$date}-{$random}";

        if (Payment::query()->where('reference_no', $referenceNo)->exists()) {
            return self::generateRefNo();
        }
        return $referenceNo;
    }
}
