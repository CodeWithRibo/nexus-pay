<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ServiceSelectionController extends Controller
{
    public function __invoke(Request $request, ?string $isLoggedIn = null )
    {
        $urlFlag = filter_var($isLoggedIn, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $actualAuth = auth()->check();

        return Inertia::render('kiosk/Services/Selection', [
            'isLoggedInFromUrl' => $urlFlag,
            'isLoggedIn' => $actualAuth,
        ]);
    }
}
