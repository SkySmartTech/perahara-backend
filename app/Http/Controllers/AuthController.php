<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'username'        => ['required', 'string', 'alpha_dash', 'min:3', 'max:30', 'unique:users,username'],
            'first_name'      => ['required', 'string', 'min:3', 'max:50'],
            'last_name'       => ['required', 'string', 'min:2', 'max:50'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['required', 'string', 'max:15'],
            'password'        => ['required', 'confirmed', 'min:8'],
            'user_type'       => ['required', Rule::in(['user', 'organizer', 'service_provider'])],
        ]);

        // Prevent admin account creation via API
        if (($data['user_type'] ?? null) === 'admin') {
            return response()->json(['message' => 'Admin accounts cannot be created via API'], 403);
        }

        // Create user
        $user = User::create([
            'username'        => $data['username'],
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'password'        => Hash::make($data['password']),
            'user_type'       => $data['user_type'],
        ]);

        // Return response
        return response()->json([
            'message' => 'Registered successfully',
            'user'    => [
                'id'         => $user->id,
                'username'   => $user->username,
                'email'      => $user->email,
                'user_type'  => $user->user_type,
            ],
        ], 201);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

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
            'first_name' => $u->first_name,
            'last_name' => $u->last_name,
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

        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'min:3'],
            'last_name'  => ['sometimes', 'string', 'min:2'],
            'username'   => ['sometimes', 'string', 'alpha_dash', 'min:3', 'max:30', Rule::unique('users')->ignore($user->id)],
            'email'      => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'      => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
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
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        unset($data['current_password']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // ✅ Handle new avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'service' => $user->fresh(),
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
