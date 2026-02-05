<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


#[OA\Tag(name: 'Auth', description: 'Authentication')]
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', minLength: 8),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User registered'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generate JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Send welcome email with token
        $user->notify(new \App\Notifications\WelcomeNotification($token));

        return response()->json([
            'message' => 'User registered successfully. A welcome email with your access token has been sent to your email.',
            'user' => $user,
        ], 201);
    }

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Login and retrieve an API token',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Authenticated'),
            new OA\Response(response: 422, description: 'Validation / auth error'),
        ]
    )]
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout current user (revoke token)',
        tags: ['Auth'],
        security: [['bearer' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logged out'),
        ]
    )]
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to logout'], 500);
        }
    }

    #[OA\Post(
        path: '/api/auth/forgot-password',
        summary: 'Request password reset OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Reset OTP sent'),
            new OA\Response(response: 422, description: 'Error sending OTP'),
        ]
    )]

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => __('passwords.user')], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send Notification
        $user->notify(new \App\Notifications\ResetPasswordNotification($otp));

        return response()->json(['message' => __('passwords.sent')]);
    }


    #[OA\Post(
        path: '/api/auth/reset-password',
        summary: 'Reset password using OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['otp', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'otp', type: 'string', minLength: 6, maxLength: 6),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', minLength: 8),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Password reset'),
            new OA\Response(response: 422, description: 'Invalid OTP or data'),
        ]
    )]

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (!$record || !Hash::check($validated['otp'], $record->token)) {
            return response()->json(['message' => __('passwords.token')], 422);
        }

        // Check expiry (standard is 60 minutes)
        $expire = config('auth.passwords.users.expire', 60);
        if (now()->subMinutes($expire)->gt($record->created_at)) {
            return response()->json(['message' => __('passwords.token')], 422);
        }

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json(['message' => __('passwords.user')], 422);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        // Delete the token record
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        // Also revoke other sessions/tokens if needed
        $user->tokens()->delete();

        return response()->json(['message' => __('passwords.reset')]);
    }

}
