<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(){
        $user = User::where('id', 1)->first();
        $user->password = bcrypt('teste');
        $user->save();
        return view('admin.index');

    }

    public function home(){
        return view('admin.dashboard');
    }

    public function login(Request $request){

        if(in_array('', $request->only('email', 'password'))) {
            $json['message'] = $this->message->error('Ooops, informe todos os dados para fazer login')->render();
            return response()->json($json);
        }

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $json['message'] = $this->message->error('Ooops, informe um email válido')->render();
            return response()->json($json);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(!Auth::attempt($credentials)){
            $json['message'] = $this->message->error('Ooops, usuário e senha não conferem')->render();
            return response()->json($json);
        }

        $json['redirect'] = route('admin.home');
        return response()->json($json);

    }
    
    public function logout(){
        Auth::logout();
        return redirect()->route('admin.login');
    }

}
