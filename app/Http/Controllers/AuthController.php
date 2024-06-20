<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{


public function register(Request $request)
{

$this->validate($request,[
    'name' => 'required|string|max:255', 
    'email' => 'required|string|email|max:255|unique:users', 
    'password' => 'required|string|min:6', 
    'role_id'=>'required',
    'status_id'=>'required',
    'branch_id'=>'required',
    'warehouse_id'=>'required',
]);
// Create new User
$user = User::create([
'name' => $request->name,
'email' => $request->email,
'role_id'=>$request->role_id,
'status_id'=>$request->status_id,
  'branch_id'=>$request->branch_id,
  'warehouse_id'=>$request->warehouse_id,
'password' => bcrypt($request->password), // Hash the password
]);

// Return user data as JSON with a 201 (created) HTTP status code
return response()->json(['user' => $user], 201);
}

// Function to handle user login
public function login(Request $request)
{
// Validate incoming request fields
$request->validate([
'email' => 'required|string|email', // Email must be a string, a valid email and it is required
'password' => 'required|string', // Password must be a string and it is required
]);

// Check if the provided credentials are valid
if (!Auth::attempt($request->only('email', 'password'))) {
// If not, return error message with a 401 (Unauthorized) HTTP status code
return response()->json(['message' => 'Invalid login details'], 401);
}

// If credentials are valid, get the authenticated user
$user = $request->user();
// Create a new token for this user
$token = $user->createToken('authToken')->plainTextToken;

// Return user data and token as JSON
return response()->json(['user' => $user, 'token' => $token]);
}

// Function to handle user logout
public function logout(Request $request)
{
  // Delete all tokens for the authenticated user
  $request->user()->tokens()->delete();

   // Return success message as JSON
   return response()->json(['message' => 'Logged out']);
}
}


