<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class StoreController extends Controller{

    public function postNew(Request $r){
        // Ensure the user doesn't have other stores
        if(auth()->user()->hasStore()){
            return $this->api_response('You already have a store!', false, 406);
        }
        // There is no validation in this method, as the store title is not required
        $Rules = [
            'vat' => 'required|integer',
            'shipping' => 'required|integer'
        ];
        $Validator = Validator::make($r->all(), $Rules);
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        $StoreData = $r->all();
        $StoreData['title'] = ($r->has('title')) ? $r->title : auth()->user()->name ."'s Store";
        $StoreData['user_id'] = auth()->user()->id;
        // Create the store
        $Store = Store::create($StoreData);
        if($Store){
            // The store has been created
            return $this->api_response($Store, true, 201);
        }else{
            // Something went horribly wrong!
            return $this->api_response('Something went wrong! the store was not created.', false, 500);
        }
    }
}
