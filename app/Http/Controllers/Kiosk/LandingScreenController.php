<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingScreenController extends Controller
{
    public function __invoke(Request $request, ?string $isLoggedIn = null)
    {
        $urlFlag = filter_var($isLoggedIn, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $actualAuth = auth()->check();

        usleep(1500000);
        return Inertia::render('kiosk/LandingScreen', [
            'isLoggedInFromUrl' => $urlFlag,
            'isLoggedIn' => $actualAuth,
        ]);
    }
}
