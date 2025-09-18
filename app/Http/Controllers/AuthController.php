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
    public function register(Request $request)
    {
        $data = $request->validate([
            'username'        => ['required','string','alpha_dash','min:3','max:30','unique:users,username'],
            'email'           => ['required','email','max:255','unique:users,email'],
            'password'        => ['required','confirmed','min:8'],
            'user_type'       => ['required', Rule::in(['user','organizer','service_provider'])],
            'service_type_id' => ['nullable','exists:service_types,id'],
        ]);

        
        if ($data['user_type'] !== 'service_provider') {
            $data['service_type_id'] = null;
        } else {
            $request->validate([
                'service_type_id' => ['required','exists:service_types,id']
            ]);
        }

        
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
        
        $loginRecord = UserLogin::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'login_at'   => Carbon::now(),
        ]);

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
            ],
            'redirect_to' => $redirect
        ]);
    }

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
            'avatar'          => $u->avatar,
            'phone'           => $u->phone,
        ]);
    }

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

        // Handle avatar upload if it's a file (you might want to implement this separately)
        // For now, we'll assume it's a URL or stored elsewhere

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'service_type_id' => $user->service_type_id,
                'service_type' => $user->serviceType?->name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $request->user()->currentAccessToken()->delete();

        $lastLogin = UserLogin::where('user_id', $user->id)
                    ->latest('login_at')
                    ->first();

        if ($lastLogin && !$lastLogin->logout_at) {
            $lastLogin->update(['logout_at' => now()]);
        }

        return response()->json(['message' => 'Logged out']);
    }
}