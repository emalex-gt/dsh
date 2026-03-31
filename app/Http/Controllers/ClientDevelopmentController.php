<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDevelopmentController extends Controller
{
    public function show(Request $request, string $section = 'hosting'): View
    {
        abort_unless(in_array($section, ['hosting', 'dominio', 'email'], true), 404);

        $user = $request->user()->load('emailAccounts');

        return view('client.development.show', [
            'section' => $section,
            'user' => $user,
        ]);
    }
}
