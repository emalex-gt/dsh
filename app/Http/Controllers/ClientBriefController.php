<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientBriefRequest;
use App\Models\Brief;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClientBriefController extends Controller
{
    public function edit(): View
    {
        return view('briefs.edit', [
            'brief' => request()->user()?->briefs()->latest('submitted_at')->latest('id')->first(),
        ]);
    }

    public function show(Request $request): View
    {
        return view('briefs.show', [
            'brief' => $request->user()->briefs()->latest('submitted_at')->latest('id')->first(),
        ]);
    }

    public function showClientData(Request $request): View
    {
        return view('client.data', [
            'user' => $request->user(),
        ]);
    }

    public function thanks(): View
    {
        return view('briefs.thanks');
    }

    public function update(StoreClientBriefRequest $request): RedirectResponse
    {
        Brief::create(
            [
                'user_id' => $request->user()?->id,
                'status' => 'submitted',
                'data' => $request->validatedBriefData(),
                'submitted_at' => Carbon::now(),
            ],
        );

        return redirect()
            ->route('brief.thanks');
    }
}
