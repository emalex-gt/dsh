<?php

namespace App\Http\Controllers;

use App\Models\Demo;
use Illuminate\View\View;

class ClientDemoController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $demos = $user->demos()
            ->with('category')
            ->orderByPivotDesc('is_recommended')
            ->orderBy('name')
            ->get();

        $recommendedDemo = $demos->first(fn (Demo $demo) => (bool) $demo->pivot?->is_recommended);

        $categorizedDemos = $demos
            ->reject(fn (Demo $demo) => $recommendedDemo && $demo->is($recommendedDemo))
            ->groupBy(fn (Demo $demo): string => $demo->category?->name ?: 'Sin categoria');

        $hasDirectDemo = filled($user->direct_demo_name)
            || filled($user->direct_demo_link)
            || filled($user->direct_demo_desktop_image_url)
            || filled($user->direct_demo_mobile_image_url);

        return view('client.demos.index', [
            'directDemo' => $hasDirectDemo ? $user : null,
            'recommendedDemo' => $recommendedDemo,
            'categorizedDemos' => $categorizedDemos,
        ]);
    }
}
