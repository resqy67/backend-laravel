<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Resources\authResource;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    //
    public function login(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new authResource(false, $validator->errors(), null);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return new authResource(false, 'Unauthorized', null);
        }

        $datauser = User::where('email', $request->email)->first();
        $token = [
            'token' => $datauser->createToken('token')->plainTextToken,
        ];
        return new authResource(true, 'Login Success', $token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nisn' => 'required|integer|unique:users',
            'class' => 'required|string',
            'description' => 'required|string',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $avatar = $request->file('avatar');
        $avatar->storeAs('public/users/avatars', $avatar->hashName());

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'nisn' => $request->nisn,
            'class' => $request->class,
            'description' => $request->description,
            'avatar' => $avatar->hashName(),
        ]);

        return new authResource(true, 'Register Success', null);
    }
}
