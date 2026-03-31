<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\StudentBalance;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CashInsertionController extends Controller
{
    public function __invoke()
    {
        $studAmountDue = StudentBalance::query()
        ->where('user_id', auth()->id())
        ->where('fee_name', 'Tuition Fee')
        ->sum('total_amount');

        return Inertia::render('kiosk/CashInsertion', compact('studAmountDue'));
    }
}
