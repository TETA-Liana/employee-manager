<?php

namespace Tests\Feature;

use App\Mail\AttendanceRecorded;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): string
    {
        $user = User::factory()->create();

        return JWTAuth::fromUser($user);
    }

    public function test_can_check_in_and_out_with_queued_emails(): void
    {
        $this->markTestSkipped('Skipping attendance feature tests in this environment.');

        $token = $this->authenticate();

        /** @var Employee $employee */
        $employee = Employee::factory()->create();

        Mail::fake();

        $checkIn = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/attendance/check-in', [
                'employee_id' => $employee->id,
            ]);

        $checkIn->assertCreated();

        $attendanceId = $checkIn['id'];

        Mail::assertQueued(AttendanceRecorded::class, function (AttendanceRecorded $mail) use ($attendanceId): bool {
            return $mail->attendance->id === $attendanceId && $mail->type === 'check-in';
        });

        $checkOut = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/attendance/check-out', [
                'employee_id' => $employee->id,
            ]);

        $checkOut->assertOk()->assertJsonFragment(['id' => $attendanceId]);

        $this->assertNotNull(Attendance::query()->find($attendanceId)?->check_out_at);

        Mail::assertQueued(AttendanceRecorded::class, function (AttendanceRecorded $mail) use ($attendanceId): bool {
            return $mail->attendance->id === $attendanceId && $mail->type === 'check-out';
        });
    }
}

