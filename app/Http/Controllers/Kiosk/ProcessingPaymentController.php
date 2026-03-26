<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProcessingPaymentController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('kiosk/ProcessingPayment');
    }
}
