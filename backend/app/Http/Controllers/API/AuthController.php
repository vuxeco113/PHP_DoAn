<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "confirm_password" => "required|same:password",
            "role" => "required|in:owner,tenant"
        ]);

        // 2. Nếu xác thực thất bại → trả lỗi JSON
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "validation errors.",
                "data" => $validator->errors()->all()
            ]);
        }

        // 3. Tạo user mới
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "role" => $request->role    
        ]);

        // 4. Tạo token đăng nhập
        $response = [];
        $response["token"] = $user->createToken("MyApp")->plainTextToken;
        $response["name"] = $user->name;
        $response["email"] = $user->email;
        $response["role"] = $user->role;
        $response["id"]=$user->id;
        // 5. Trả JSON phản hồi thành công
        return response()->json([
            "status" => 1,
            "message" => "user registered",
            "data" => $response
        ]);
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response = [];
            $response["token"] = $user->createToken("MyApp")->plainTextToken;
            $response["name"] = $user->name;
            $response["email"] = $user->email;
            $response["role"]=$user->role;
            $response["id"]=$user->id;
            return response()->json([
                'status' => 1,
                'message' => 'user login',
                'data' => $response,
            ]);
        }
    }



}
