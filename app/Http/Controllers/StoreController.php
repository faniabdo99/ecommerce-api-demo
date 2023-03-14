<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class StoreController extends Controller{

    /**
     * @param Request $r
     * @description Create a new store for the user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages POST /api/v1/store/create
     */
    public function postNew(Request $r){
        // Ensure the user doesn't have other stores
        if(auth()->user()->hasStore()){
            return $this->api_response('You already have a store!', false, 406);
        }
        $Rules = [
            'vat_percentage' => 'required|integer',
            'shipping' => 'required|integer'
        ];
        $Validator = Validator::make($r->all(), $Rules);
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        $StoreData = $r->all();
        // Generate an automated title unless the user did supply a title (Defaults to USER_NAME's Store)
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
