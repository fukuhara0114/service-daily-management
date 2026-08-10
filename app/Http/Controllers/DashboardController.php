<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'user' => auth()->user(),
            'activitiesUrl' => route('daily-activities.index'),
            'upsertUrl' => route('daily-activities.upsert'),
        ]);
    }
}
