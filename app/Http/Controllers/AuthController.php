<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){
        if (!Auth::guard("web")->attempt($request->all())){
			return response(
				["message" => "incorrect password or username"],
				401
			);
		}
		$user = request()->user();
        $user->token = $user->createToken("customer")->plainTextToken;
		return $user;
    }

    public function register(RegisterRequest $request){
        $role = Role::where(["name" => "customer"])->firstOrCreate([
            "name" => "customer"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "role_id" => $role->id,
        ]);

        $user->token = $user->createToken("customer")->plainTextToken;
    
        return $user;
    }
}
