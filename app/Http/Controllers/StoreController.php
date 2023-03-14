<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;

class StoreController extends Controller{

    public function postNew(Request $r){
        // Ensure the user doesn't have other stores
        if(auth()->user()->hasStore()){
            return $this->api_response('You already have a store!', false, 406);
        }
        // There is no validation in this method, as the store title is not required
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
