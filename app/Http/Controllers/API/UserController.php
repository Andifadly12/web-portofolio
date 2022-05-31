<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Rules\Password as RulesPassword;

class UserController extends Controller
{
    //membuar method register
    public function register(Request $request)
    {
        //fungsi register menggunakan block rekesc jika agagar kita akna di keluarkan otometis dan jika berhasil maka kita dapat login dan mendaftar
        try{
            //melakuakna validasi 
            $request->validate([
                'name'=>['required','string','max:255'],
                //memakai unique karna username setiap pengguna tidak boleh sama
                'username'=>['required','string','max:255','unique:users'],
                //memakai email agar menandakan dai adalah email
                'email'=>['required','string','email','max:255','unique:users'],
                'phone'=>['nullable','string','max:255'],
                //untuk validasi password
                'password'=>['required','string', new RulesPassword],
            ]);
            //intuk membuar usernya
            User::create([
                'name'=>$request->name,
                'username'=>$request->username,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'password'=>Hash::make($request->password),
            ]);
            //untuk memasukkan data ketable user dan mengaambil berdasarkan email karna email aunique
            $user=User::where('email',$request->email)->first();
            //membuat token
            //plianTextToken adalah untuk memngembalikan token
            $tokenResult=$user->createToken('authToken')->plainTextToken;
            //jika succes makan akan mengembalikan accces token, token type dan isi dari user yang diatas
            return ResponseFormatter::success([
                'acces_token'=>$tokenResult,
                'token_type'=>'Bearer',
                'user'=>$user
            ], 'Users berhasil ditambah'
        );

        }catch(Exception $error){
            return ResponseFormatter::error([
                'message'=>'some went wrong',
                'error'=>$error
            ], 'Authentication Failed', 500
        );
        }
    }
    //membuat method untuk login
    public function login(Request $request){
        //membuat validasi untuk emal dan password
        try{
            $request->validate([
                'email'=>'email|required',
                'password'=>'required'
            ]);

            //untuk mengetahui apakah password dan email benar atau salah dengan di tampung di variable $credentials
            $credentials=request(['email','password']);
            //jika emailnya salah akan dijalankan yang di bawah berikut ini dan akan di kembalikan 
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                'message'=>'Unauthorized' 
                ], 'Authentication Failed', 500);
            }
            //jika emailnya benar akna dijalankan variable tersebut 
            $user=User::where('email', $request->email)->first();

            //pengecekan apakah passwordnya sesuai atau tidak
            if(!Hash::check($request->password, $user->password,[])){
                //akan masuk ke catca jika tidak berhasil
                throw new \Exception('invalid credentials');
            }
            //jika berhasil maka akan di jalankan $tokenResult dan mengembalikan plainTextToken
            $tokenResult=$user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token'=>$tokenResult,
                'token_type'=>'Bearer',
                'user'=>$user
            ],'Authenticated');
        }catch(Exception $error){
            return ResponseFormatter::error([
                'message'=>'something went erong',
                'error'=>$error,
            ], 'Authentication Failed', 500);
        }
    }
    //membuat method fetch
    public function fetch(Request $request)
    {
        //untuk mengecek apkah sudalogin jika sudah login maka dapat memanggil method tersebut
        return ResponseFormatter::success($request->user(), 'Data profil user berhasil diambil');
    }
    //untuk mengpdate profile
    public function updateProfile(Request $request){
        try{
            //melakuakna validasi 
            $request->validate([
                'name'=>['required','string','max:255'],
                //memakai unique karna username setiap pengguna tidak boleh sama
                'username'=>['required','string','max:255','unique:users'],
                //memakai email agar menandakan dai adalah email
                'email'=>['required','string','email','max:255','unique:users'],
                'phone'=>['nullable','string','max:255'],
            ]);
            $user=Auth::user();
            User::update([
                'name'=>$request->name,
                'username'=>$request->username,
                'email'=>$request->email,
                'phone'=>$request->phone,
            ]);
            //untuk memasukkan data ketable user dan mengaambil berdasarkan email karna email aunique
            $user=User::where('email',$request->email)->first();
            //membuat token
            //plianTextToken adalah untuk memngembalikan token
            $tokenResult=$user->createToken('authToken')->plainTextToken;
            //jika succes makan akan mengembalikan accces token, token type dan isi dari user yang diatas
            return ResponseFormatter::success([
                'acces_token'=>$tokenResult,
                'token_type'=>'Bearer',
                'user'=>$user
            ], 'Users berhasil diupdate'
        );
        }catch(Exception $error){
            return ResponseFormatter::error([
                'message'=>'some went wrong',
                'error'=>$error
            ], 'Authentication Failed', 500
        );
        }
    
    }
    //membuat method lougout
    public function logout(Request $request){
        //mengambil user yang sedang login dan curreentAccessToken untuk mengetahui siapa yg sedang memakai aksestoken sekarang dan dihapus
        $token=$request->user()->currentAccessToken()->delete();
        //untuk menjalankan proses logout
        return ResponseFormatter::success($token, 'token Revoked');
    }
}
