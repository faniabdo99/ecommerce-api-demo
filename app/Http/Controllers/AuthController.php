<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            $User = User::create($UserData);
            return $this->api_response(['user' => $User], true, 200);
        }
    }
}
