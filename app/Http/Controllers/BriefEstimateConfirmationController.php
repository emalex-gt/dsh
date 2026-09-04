<?php

namespace App\Http\Controllers;

use App\Services\BriefEstimateConfirmationService;
use App\Http\Requests\ConfirmBriefEstimateRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BriefEstimateConfirmationController extends Controller
{
    public function show(string $token, BriefEstimateConfirmationService $service): View
    {
        return view('briefs.review-estimate', ['brief' => $service->findAvailable($token), 'token' => $token]);
    }

    public function adjust(string $token, BriefEstimateConfirmationService $service): View
    {
        return view('briefs.edit', [
            'brief' => $service->findAvailable($token),
            'pendingToken' => $token,
            'initialStepKey' => 'servicios',
            'preselectedServiceIds' => [],
            'serviceCatalog' => app(ClientBriefController::class)->serviceCatalogForBrief(),
            'technicalQuoteRules' => app(ClientBriefController::class)->technicalQuoteRulesForBrief(),
        ]);
    }

    public function confirm(string $token, ConfirmBriefEstimateRequest $request, BriefEstimateConfirmationService $service): RedirectResponse
    {
        $user = $service->confirm($token, $request->string('confirmed_name')->toString(), (string) $request->ip(), (string) $request->userAgent());
        Password::sendResetLink(['email' => $user->email]);
        return redirect()->route('brief.thanks');
    }
}
