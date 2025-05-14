<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index()
    {

        $data = User::all();
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'User found',
                'user' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
    }

    public function registrasi(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload avatar jika ada
        $avatar = $request->file('avatar');
        $avatarName = time() . '_' . $avatar->getClientOriginalName();
        $avatar->move(public_path('avatar'), $avatarName);

        // Simpan path-nya ke database (misal: avatar/nama_file.jpg)
        $avatarPath =  $avatarName;

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'avatar' => $avatarPath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function tambah_user(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            "avatar" => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',



        ]);
        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }
        // Upload avatar jika ada
        $avatar = $request->file('avatar');
        $avatarName = time() . '_' . $avatar->getClientOriginalName();
        $avatar->move(public_path('avatar'), $avatarName);

        // Jika validasi sukses, buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt("12345678"),
            'role' => "user",
            'avatar' => $avatarName,
        ]);
        // Response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }

        // Cek kredensial pengguna
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Tidak Ada'
            ], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            // Hapus token lama
            $user->tokens()->delete();

            // Buat token baru dari user yang sudah ditemukan
            $token = $user->createToken('API Token')->plainTextToken;
            // Simpan token ke dalam database
            $user->update([
                'remember_token' => $token,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Password Salah'
            ], 401);
        }


        // Jika kredensial benar, buat token



        // Jika kredensial salah

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $data = User::find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'User found',
                'user' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        if ($request->hasFile('avatar')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,user',
                'password' => 'nullable|min:8',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,user',
                'password' => 'nullable|min:8',

            ]);
        }


        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }

        // Jika validasi sukses, update user


        $user = User::find($id);

        if ($user) {


            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    $avatarPath = public_path('avatar/' . $user->avatar);
                    if (file_exists($avatarPath)) {
                        unlink($avatarPath);
                    }
                }


                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $avatar->getClientOriginalName();
                $avatar->move(public_path('avatar'), $avatarName);
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password ? bcrypt($request->password) : $user->password,
                    'role' => $request->role,
                    'avatar' => $avatarName,
                ]);
                # code...
            } else {
                # code...

                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password ? bcrypt($request->password) : $user->password,
                    'role' => $request->role,
                ]);
            }


            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $user
            ], 200); // HTTP 200 OK
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ],);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            // Hapus avatar jika ada
            if ($user->avatar) {
                $avatarPath = public_path('avatar/' . $user->avatar);
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                }
            }
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ], 200); // HTTP 200 OK
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404); // HTTP 404 Not Found
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful'
        ], 200); // HTTP 200 OK
    }
}
