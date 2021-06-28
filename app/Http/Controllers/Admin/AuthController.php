<?php

namespace App\Http\Controllers\Admin;

use App\Contract;
use App\Http\Controllers\Controller;
use App\Property;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(){

        if(Auth::check() === true) {
            return redirect()->route('admin.home');
        }

        return view('admin.index');

    }

    public function home(){

        $lessors = User::lessors()->count();
        $lessees = User::lessees()->count();
        $team = User::where('admin', 1)->count();

        $propertiesAvailable = Property::available()->count();
        $propertiesUnavailable = Property::unavailable()->count();
        $propertiesTotal = Property::all()->count();

        $contractsPendent = Contract::pendent()->count();
        $contractsActive = Contract::active()->count();
        $contractsCanceled= Contract::canceled()->count();
        $contractsTotal = Contract::all()->count();

        $contracts = Contract::orderBy('id', 'DESC')->limit(10)->get();

        $properties = Property::orderBy('id', 'DESC')->limit(3)->get();

        return view('admin.dashboard', compact('lessors', 'lessees', 'team', 'propertiesAvailable', 'propertiesUnavailable', 'propertiesTotal',
        'contractsPendent', 'contractsActive', 'contractsCanceled', 'contractsTotal', 'contracts', 'properties'));
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

        $this->authenticated($request->getClientIp());
        $json['redirect'] = route('admin.home');
        return response()->json($json);

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('admin.login');
    }

    private function authenticated(string $ip){
        $user = User::where('id', Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ]);
    }

}
