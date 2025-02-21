<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Validator;
use Hash;
use Exception;
use JWTAuth;

class AuthController extends Controller
{
    //In minutes
    final const ACCESS_KEY_TTL = 259200;

    public function __construct()
    {
        $this->middleware(['jwt.auth'], ['except' => ['login', 'register','resetPassword','forgotPassword']]);
        //$this->middleware('throttle:6,1')->only('refresh','login', 'register');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }

        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json([
                "success" => false,
                "message" => "Invalid email or password",
            ], 422);
        }

        $user = auth('api')->user();
        $user['image'] =  url('assets/img/users').'/'.$user->image;

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in',
            'api_token' => [
                'refresh_token' => auth('api')->setTTL($this::ACCESS_KEY_TTL)->fromUser($user), 
                'access_token' => $token,
            ],
            'user' => $user,
        ]);

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'phone' => 'required|max:20|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            "accepted_terms" => 'required'
        ]);

       if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => implode("\n", $validator->errors()->all()),
            ], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->password)]
        ));

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered.',
        ], 201);
    }

    public function logout(Request $request)
    {
        $refreshToken = $request->refresh_token;

        auth('api')->setToken($refreshToken)->invalidate();
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ]);
    }

    public function refresh(Request $request)
    {
        try {
            $user = auth('api')->userOrFail();

            return response()->json([
                'access_token' => auth('api')->fromUser($user),
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Token refresh failed'], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if($status !== Password::RESET_LINK_SENT){
            abort(500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset link has been sent successfully, check your mailbox.'
        ]);
    }
}
