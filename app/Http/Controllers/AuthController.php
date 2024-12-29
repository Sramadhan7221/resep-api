<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {

        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|confirmed'
            ];
    
            $message = [
                'name.required' => 'Nama pengguna harus diisi',
                'email.required' => 'Email harus diisi',
                'email.unique' => 'Email sudah digunakan, silahkan masukan email yang lain',
                'password.required' => 'Password harus diisi',
                'password.confirmed' => 'Konfirmasi password tidak cocok'
            ];
    
            $validated = Validator::make($request->all(), $rules, $message);
            if($validated->fails()){
                $error = implode(", ", array_map('implode', array_values($validated->errors()->messages())));
                return response()->json([
                    'msg_type' => 'warning',
                    'message' => $error
                ],400);
            }
    
            $validate = $validated->validate();
            $user = User::create($validate);
    
            event(new Registered($user));
            return response()->json([
                'msg_type' => "success",
                'message' => 'Berhasil daftar, silahkan to verify'
            ],200);

        } catch (\Throwable $th) {
            
            Log::error('Kesalahan Sistem: ' . $th->getMessage());
            return response()->json([
                'msg_type' => "error",
                'message' => 'Terjadi Kesalahan'
            ],500);
        }

    }

    public function __invoke(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect("https://ecotourism-staging.labtekcmr.com/authentication/verification");
        // return response()->json([
        //     'message' => 'Email verified successfully.',
        // ], 200);
    }

    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'message' => 'Email tidak terdaftar',
                'msg_type' => 'warning'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email sudah digunakan',
                'msg_type' => "error",
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link konfirmasi berhasil dikirim',
        ], 200);
    }

    public function login(Request $request){
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $message = [
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ];

        $validated = Validator::make($request->all(), $rules, $message);
        if($validated->fails()){
            $error = implode(", ", array_map('implode', array_values($validated->errors()->messages())));
            return response()->json([
                'msg_type' => "warning",
                'message' => $error
            ],400);
        }
        $user = User::with('roles')->where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'msg_type' => "warning",
                'message' => 'Email atau password salah'
            ],404);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'msg_type' => "warning",
                'message' => 'Email atau password salah'
            ]);
        }

        if(!$user->hasVerifiedEmail())
            return response()->json([
                'msg_type' => "error",
                'message' => "Silahkan konfirmasi email terlebih dahulu"
            ],400);

        $token = $user->createToken('auth_token', ['*'], now()->addDay());
            return response()->json([
                'status' => true,
                'message' => 'Login Berhasil',
                'token' => 'Bearer ' . $token->plainTextToken
            ]);
    }

    
    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
