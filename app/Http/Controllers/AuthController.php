<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

// Models
use App\User;

class AuthController extends Controller{
    /**
     * @param Request $r
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @description Creates a new user record in the database
     * @usages POST /api/v1/auth/signup
     */
    public function postSignup(Request $r){
        // Validate the request
        $Rules = [
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5'
        ];
        $Validator = Validator::make($r->all(), $Rules);
        if($Validator->fails()){
            // Return validation errors
            return $this->api_response($Validator->errors(), false, 422);
        }else{
            // Save the user to teh database
            $UserData = $r->all();
            $UserData['password'] = Hash::make($r->password);
            $UserData['api_token'] = Str::random(60);
            $User = User::create($UserData);
            return $this->api_response(['user' => $User], true, 200);
        }
    }

    /**
     * @param Request $r
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @description Log a user in by comparing the incoming data with our database and return an authentication token
     * @usages POST /api/v1/auth/login
     */
    public function postLogin(Request $r){
        // Validate the request
        $Rules = [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ];
        $Validator = Validator::make($r->all(), $Rules);
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }else{
            // Attempt to log the user in
            if(Auth::attempt($r->all())){
                // The user is allowed to login
                $AuthenticatedUser = User::where('email', $r->email)->first();
                return $this->api_response(['user' => $AuthenticatedUser, 'token' => $AuthenticatedUser->api_token], true, 200);
            }else{
                return $this->api_response('The email or password you entered are incorrect!', false, 401);
            }
        }
    }
}
