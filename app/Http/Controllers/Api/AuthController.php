<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Resources\AuthResource;
use App\Models\Loan;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     *  Login user
     *
     * Logging in user
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            /**
             * @example admin@mail.com
             */
            'email' => 'required|string|email',
            /**
             * @example password
             */
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new AuthResource(false, $validator->errors(), null);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(new AuthResource(false, 'Unauthorized', null), 401);
        }

        $datauser = User::where('email', $request->email)->first();
        $token = [
            'token' => $datauser->createToken('token')->plainTextToken,
        ];
        return new AuthResource(true, 'Login Success', $token);
    }

    /**
     *  Register user
     *
     * Registering new user
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nisn' => 'required|integer|unique:users',
            'class' => 'required|string',
            'description' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        // $avatar = $request->file('avatar');
        // $avatar->storeAs('public/users/avatars', $avatar->hashName());
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            // $avatarPath = $avatar->storeAs($avatar->hashName());
            $avatar->storeAs('public/users/avatars', $avatar->hashName());
            $avatarPath = $avatar->hashName();
        }



        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'nisn' => $request->nisn,
            'class' => $request->class,
            'description' => $request->description,
            'avatar' => $avatarPath,
        ]);

        return new AuthResource(true, 'Register Success', null);
    }

    /**
     *  info user
     *
     * Get detail user
     *
     */
    public function getUser(Request $request)
    {
        $user = $request->user();
        return new AuthResource(true, 'Data retrieved successfully', $user);
    }

    /**
     *  add token fcm
     *
     * Add token fcm to user
     *
     */
    /**
     *  add token fcm
     *
     * Add token fcm to user
     *
     */
    public function addTokenFcm(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token_fcm' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        User::where('id', $request->user()->id)->update([
            'token_fcm' => $request->token_fcm,
        ]);

        return new AuthResource(true, 'Token FCM added successfully', null);
    }

    /**
     *  logout user
     *
     * Logging out user
     *
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return new AuthResource(true, 'Logout Success', null);
    }

    /**
     * get all users
     *
     * Get all users
     */
    public function getUsers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $users = User::latest()->simplePaginate($perPage, ['*'], 'page', $page);
        return new AuthResource(true, 'Data retrieved successfully', $users);
    }

    /**
     * Reset password User
     *
     * Reset password user by email user
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return new AuthResource(false, 'Email not found', null);
        }

        $user->update([
            'password' => bcrypt('password'),
        ]);

        return new AuthResource(true, 'Password reset successfully', null);
    }

    /**
     * Delete user
     *
     * Delete user by id
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return new AuthResource(true, 'User deleted successfully', null);
        }
        return (new AuthResource(false, 'User not found', null))->response()->setStatusCode(404);
    }
}