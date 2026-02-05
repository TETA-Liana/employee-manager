<?php

namespace App\Http\Controllers;

use App\Exports\DailyAttendanceExport;
use App\Models\Attendance;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reports', description: 'Attendance reports')]
class AttendanceReportController extends Controller
{
    #[OA\Get(
        path: '/api/reports/attendance/daily/pdf',
        summary: 'Download daily attendance report as PDF',
        tags: ['Reports'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'PDF file'
            ),
        ]
    )]
    public function dailyPdf(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $attendances = Attendance::with('employee')
            ->whereDate('check_in_at', $date)
            ->get();

        $pdf = SnappyPdf::loadView('reports.daily_attendance', [
            'date' => $date,
            'attendances' => $attendances,
        ]);

        $filename = Str::of('attendance-'.$date.'.pdf')->replace(' ', '_')->toString();

        return $pdf->download($filename);
    }

    #[OA\Get(
        path: '/api/reports/attendance/daily/excel',
        summary: 'Download daily attendance report as Excel',
        tags: ['Reports'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Excel file'
            ),
        ]
    )]
    public function dailyExcel(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $filename = 'attendance-'.$date.'.xlsx';

        return Excel::download(new DailyAttendanceExport($date), $filename);
    }
}

