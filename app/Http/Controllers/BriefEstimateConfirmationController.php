<?php

namespace App\Http\Controllers;

use App\Services\BriefEstimateConfirmationService;
use Illuminate\View\View;

class BriefEstimateConfirmationController extends Controller
{
    public function show(string $token, BriefEstimateConfirmationService $service): View
    {
        return view('briefs.review-estimate', ['brief' => $service->findAvailable($token), 'token' => $token]);
    }
}
