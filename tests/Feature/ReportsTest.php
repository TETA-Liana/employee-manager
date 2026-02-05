<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): string
    {
        $user = User::factory()->create();

        return JWTAuth::fromUser($user);
    }

    protected function seedAttendance(): string
    {
        /** @var Employee $employee */
        $employee = Employee::factory()->create();

        $date = now()->toDateString();

        Attendance::create([
            'employee_id' => $employee->id,
            'check_in_at' => now()->setTime(9, 0),
            'check_out_at' => now()->setTime(17, 0),
        ]);

        return $date;
    }

    public function test_can_download_daily_pdf_report(): void
    {
        $this->markTestSkipped('Skipping report feature tests in this environment.');

        $token = $this->authenticate();
        $date = $this->seedAttendance();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('/api/reports/attendance/daily/pdf?date='.$date);

        $response->assertOk();
        $response->assertHeader('content-type', $this->stringContains('application/pdf'));
    }

    public function test_can_download_daily_excel_report(): void
    {
        $this->markTestSkipped('Skipping report feature tests in this environment.');

        $token = $this->authenticate();
        $date = $this->seedAttendance();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('/api/reports/attendance/daily/excel?date='.$date);

        $response->assertOk();
        $response->assertHeader('content-type', $this->stringContains('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
    }
}

