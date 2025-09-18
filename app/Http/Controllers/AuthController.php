<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'username'        => ['required','string','alpha_dash','min:3','max:30','unique:users,username'],
            'email'           => ['required','email','max:255','unique:users,email'],
            'password'        => ['required','confirmed','min:8'],
            'user_type'       => ['required', Rule::in(['user','organizer','service_provider'])],
            'service_type_id' => ['nullable','exists:service_types,id'],
        ]);

        // Only service_provider can have service_type_id
        if ($data['user_type'] !== 'service_provider') {
            $data['service_type_id'] = null;
        } else {
            $request->validate([
                'service_type_id' => ['required','exists:service_types,id']
            ]);
        }

        // Prevent API registration for admin accounts
        if (($data['user_type'] ?? null) === 'admin') {
            return response()->json(['message' => 'Admin accounts cannot be created via API'], 403);
        }

        $user = User::create([
            'username'        => $data['username'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'user_type'       => $data['user_type'],
            'service_type_id' => $data['service_type_id'] ?? null,
        ]);

        return response()->json(['message' => 'Registered successfully'], 201);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // Record login
        UserLogin::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'login_at'   => Carbon::now(),
        ]);

        // Determine redirect path based on user type
        $redirect = match ($user->user_type) {
            'admin'            => '/admin/dashboard',
            'organizer'        => '/organizer/dashboard',
            'service_provider' => '/service/dashboard',
            default            => '/user/dashboard',
        };

        return response()->json([
            'token'       => $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'              => $user->id,
                'username'        => $user->username,
                'email'           => $user->email,
                'user_type'       => $user->user_type,
                'service_type_id' => $user->service_type_id,
                'service_type'    => $user->serviceType?->name,
                'phone'           => $user->phone,
                'avatar'          => $user->avatar,
            ],
            'redirect_to' => $redirect
        ]);
    }

    /**
     * Get currently authenticated user
     */
    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'              => $u->id,
            'username'        => $u->username,
            'email'           => $u->email,
            'user_type'       => $u->user_type,
            'service_type_id' => $u->service_type_id,
            'service_type'    => $u->serviceType?->name,
            'phone'           => $u->phone,
            'avatar'          => $u->avatar,
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'username' => [
                'sometimes',
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:30',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'current_password' => [
                'required_with:password',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                }
            ],
            'password' => ['sometimes', 'confirmed', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Remove current_password from update data
        unset($data['current_password']);

        // Hash new password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id'              => $user->id,
                'username'        => $user->username,
                'email'           => $user->email,
                'user_type'       => $user->user_type,
                'service_type_id' => $user->service_type_id,
                'service_type'    => $user->serviceType?->name,
                'phone'           => $user->phone,
                'avatar'          => $user->avatar,
            ]
        ]);
    }

    /**
     * Logout user and delete current token
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Delete current token
        $user->currentAccessToken()->delete();

        // Update last login record with logout time
        $lastLogin = UserLogin::where('user_id', $user->id)
                    ->latest('login_at')
                    ->first();

        if ($lastLogin && !$lastLogin->logout_at) {
            $lastLogin->update(['logout_at' => now()]);
        }

        return response()->json(['message' => 'Logged out']);
    }
}
