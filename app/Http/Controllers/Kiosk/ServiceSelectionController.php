<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ServiceSelectionController extends Controller
{
    public function __invoke()
    {
        usleep(1500000);

        return Inertia::render('kiosk/ServiceSelection');
    }
}
