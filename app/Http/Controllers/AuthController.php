<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
                'service_type'    => $user->serviceType?->service_type, 
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
            'service_type'    => $u->serviceType?->service_type,
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
