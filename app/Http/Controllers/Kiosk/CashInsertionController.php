<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CashInsertionController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('kiosk/CashInsertion');
    }
}
