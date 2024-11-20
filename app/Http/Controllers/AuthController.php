<?php
namespace App\Http\Controllers;
use App\Models\Apiuser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Register with Email Verification
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|string|in:male,female,other',
            'address' => 'required|string',
            'street' => 'string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Apiuser::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'status' => 'InActive',
        ]);

        // Send Verification Email
        // Logic to send an email verification (not implemented here)
        return response()->json(['message' => 'User registered successfully. Please verify your email.'], 201);
    }

    // 2. Login and return JWT token
    public function login(Request $request)
    {
    
        // $credentials = $request->only('email', 'password');
        //   dd( $credentials);
        // Check if the user is active
        $user = Apiuser::where('email', $request->email)->first();
        // dd($user);
        if (!$user || $user->status !== 'active') {
            return response()->json(['error' => 'User is not active or does not exist.'], 403);
        }
        $credentials = $request->only('email', 'password');
        // $token = JWTAuth::attempt($credentials);
        // $token = JWTAuth::attempt($credentials, ['guard' => 'api']);
        // $token = auth('api')->attempt($credentials);

        // dd($token);
        if (!$token = auth('api')->attempt($credentials)) {
            
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token], 200);
    }

    // 3. Update Profile
    public function updateProfile(Request $request)
    {
        // $user = auth('api')->user();
        $user = auth()->user();
        // dd($user);


        $validator = Validator::make($request->all(), [
            'first_name' => 'string|max:255',
            // 'last_name' => 'string|max:255',
            // 'phone_number' => 'string|max:15',
            // 'gender' => 'string|in:male,female,other',
            // 'address' => 'string',
            // 'street' => 'string',
            // 'city' => 'string',
            // 'state' => 'string',
            // 'country' => 'string',
            // 'postal_code' => 'string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->only([
            'first_name'
        ]));

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
}

