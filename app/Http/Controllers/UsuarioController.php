<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\User;
use Illuminate\Http\Request;
use  JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Validator;
use ReCaptcha\ReCaptcha;
class UsuarioController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','verificar']]);      
    }
    public function register(Request  $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = new  User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return  response()->json([
            'status' => 'ok',
            'code'=>'201',
            'data' => $user
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $input = $request->only('email', 'password');
        $jwt_token = null;
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return  response()->json([
                'status' => 'invalid_credentials',
                'code'=>'401',
                'message' => 'Correo o contraseña no válidos.',
            ]);
        }

        return  response()->json([
            'status' => 'ok',
            'code'=>'200',
            'token' => $jwt_token,
        ]);
    }
    public function logout(Request $request) {
        $user = auth()->invalidate(true);
        return  response()->json([
            'status' => 'ok',
            'code'=>'200',
            'message' => 'Cierre de sesión exitoso.'
        ]);
       
    }
    public function userProfile() {
        $user = auth()->user();
        return  response()->json([
            'status' => 'ok',
            'code'=>'200',
            'user' => $user
        ]);  
    }
    public function verificar(Request $request){
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $secret   = '6LeGE2UaAAAAAG7cNxLmyZRLr_hCMtu8VonTudvx';
        $value= $request->token;
        $recaptcha = new ReCaptcha($secret);
        $resp = $recaptcha->verify($value, $remoteip);
        if ($resp->isSuccess()) {
            return  response()->json([
                'status' => 'ok',
                'code'=>'200',
                'success' => true
            ]);            
        } else {
            return  response()->json([
                'status' => 'ok',
                'code'=> '400',
                'success' => false
            ]);  
        }
    }
}
