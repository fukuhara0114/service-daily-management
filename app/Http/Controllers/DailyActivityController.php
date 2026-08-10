<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $activities = DailyActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '>=', $validated['start'])
            ->whereDate('work_date', '<', $validated['end'])
            ->orderBy('work_date')
            ->get();

        return $activities->map(function (DailyActivity $activity) {
            return [
                'id' => $activity->id,
                'title' => sprintf(
                    '案件:%d / 案内:%d',
                    $activity->new_case_count,
                    $activity->mail_notice_count,
                ),
                'start' => $activity->work_date->format('Y-m-d'),
                'allDay' => true,
                'extendedProps' => [
                    'new_case_count' => $activity->new_case_count,
                    'mail_notice_count' => $activity->mail_notice_count,
                    'remarks' => $activity->remarks,
                ],
            ];
        });
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'new_case_count' => ['required', 'integer', 'min:0'],
            'mail_notice_count' => ['required', 'integer', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $activity = DailyActivity::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'work_date' => $validated['work_date'],
            ],
            [
                'new_case_count' => $validated['new_case_count'],
                'mail_notice_count' => $validated['mail_notice_count'],
                'remarks' => $validated['remarks'] ?? null,
            ],
        );

        return response()->json([
            'id' => $activity->id,
            'title' => sprintf(
                '案件:%d / 案内:%d',
                $activity->new_case_count,
                $activity->mail_notice_count,
            ),
            'start' => $activity->work_date->format('Y-m-d'),
            'allDay' => true,
            'extendedProps' => [
                'new_case_count' => $activity->new_case_count,
                'mail_notice_count' => $activity->mail_notice_count,
                'remarks' => $activity->remarks,
            ],
        ]);
    }
}
