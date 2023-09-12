<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Authentication"},
 *     summary="User login",
 *     operationId="login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="email", type="string", format="email", example="test@test.com"),
 *             @OA\Property(property="password", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="token", type="string", example="your-access-token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error or incorrect credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The provided data are not correct!")
 *         )
 *     )
 * )
 */
    public function login(Request $request){

        $request->validate([
             'email' => 'required|email',
             'password' => 'required|min:6']);

        $user = User::where('email',$request->email)->first();

        if (!$user)
        {
            throw ValidationException::withMessages([
                'email' => 'The provided data are not correct !'
            ]);
        }

        if(!Hash::check($request->password,$user->password))
        {
            throw ValidationException::withMessages([
                'email' => 'The provided data are not correct !'
            ]);
        }

        $token = $user->createToken('api-passeport')->accessToken;
        return response()->json(['token' => $token]);



    }

/**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Authentication"},
 *     summary="User registration",
 *     operationId="register",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="api@test.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="token", type="string", example="your-access-token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error or user already exists",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The given data was invalid.")
 *         )
 *     )
 * )
 */

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('api-passeport')->accessToken;

        return response()->json(['token' => $token], 200);
    }





/**
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Authentication"},
 *     summary="User logout",
 *     operationId="logout",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="User logged out successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User logged out!")
 *         )
 *     )
 * )
 */
    public function logout()
    {
        $user = Auth::user();

        $user->tokens->each(function ($token) {

            $token->delete();

        });

        return response()->json([
            "message" => "User Logged out !"
        ]);


    }



}
