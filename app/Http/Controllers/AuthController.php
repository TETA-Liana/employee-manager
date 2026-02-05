<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

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
            new OA\Response(
                response: 201, 
                description: 'User registered',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                    ]
                )
            ),
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

        // Send welcome email (no token)
        $user->notify(new \App\Notifications\WelcomeNotification());

        return response()->json([
            'message' => 'User registered successfully. A welcome email has been sent to your email.',
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
            new OA\Response(
                response: 200, 
                description: 'Authenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'refresh_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer'),
                    ]
                )
            ),
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

        $token = JWTAuth::claims(['type' => 'access'])->fromUser($user);

        // Generate Refresh JWT (1 hour) - Set TTL dynamically
        Config::set('jwt.ttl', config('jwt.refresh_ttl', 60));
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
        
        // Restore default TTL for subsequent operations in same request if any
        Config::set('jwt.ttl', config('jwt.ttl', 15));

        return response()->json([

            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
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
            return response()->json(['message' => 'Logged out successfully. Token invalidated.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to logout'], 500);
        }
    }



    #[OA\Post(
        path: '/api/auth/refresh',
        summary: 'Refresh the access token using a refresh token',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['refresh_token'],
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Token refreshed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'refresh_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid refresh token'),
        ]
    )]
    public function refresh(Request $request)
    {
        $validated = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $payload = JWTAuth::setToken($validated['refresh_token'])->getPayload();
            
            if ($payload->get('type') !== 'refresh') {
                return response()->json(['message' => 'Invalid token type'], 401);
            }

            $user = User::find($payload->get('sub'));
            if (!$user) {
                return response()->json(['message' => 'User not found'], 401);
            }

            // Invalidate old tokens (Access in header + Refresh just used)
            try {
                JWTAuth::setToken($validated['refresh_token'])->invalidate();
                if ($oldAccessToken = JWTAuth::getToken()) {
                    JWTAuth::invalidate($oldAccessToken);
                }
            } catch (\Exception $e) {
                // Ignore if invalidation fails
            }

            // Generate new pair
            $newAccessToken = JWTAuth::claims(['type' => 'access'])->fromUser($user);
            
            Config::set('jwt.ttl', config('jwt.refresh_ttl', 60));
            $newRefreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
            Config::set('jwt.ttl', config('jwt.ttl', 15));

            return response()->json([

                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
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

        // Delete expired OTPs globally for security
        $expire = config('auth.passwords.users.expire', 15);
        DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subMinutes($expire))
            ->delete();

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

        // Check expiry
        $expire = config('auth.passwords.users.expire', 15);
        if (now()->subMinutes($expire)->gt($record->created_at)) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
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
