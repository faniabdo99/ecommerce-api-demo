<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller {
    private function validateRequest($r, $method){
        $Rules = [];
        switch ($method){
            case 'create':
                $Rules['title'] = 'required';
                $Rules['description'] = 'required';
                $Rules['price'] = 'required|integer';
                $Rules['shipping'] = 'required|integer';
                break;
            case 'update':
                break;
            default:
                $Rules['title'] = 'required';
                $Rules['description'] = 'required';
                $Rules['price'] = 'required|integer';
                $Rules['shipping'] = 'required|integer';
        }
        return Validator::make($r, $Rules);
    }

    public function getAll(){
        return $this->api_response(Product::mine()->get(), true, 200);
    }
    public function getSingle(Product $Product){
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to view this product', false, 403);
        }
        return $this->api_response($Product, true, 200);
    }
    public function postNew(Request $r){
        // Validate the request
        $Validator = $this->validateRequest($r->all(), 'create');
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        // Validation clear, add the product
        $ProductData = $r->all();
        $ProductData['user_id'] = auth()->user()->id;
        $ProductData['store_id'] = auth()->user()->Store->id;
        $ProductData['is_vat_included'] = ($r->has('vat_percentage')) ? 1 : 0;
        $ProductData['vat_percentage'] = ($r->has('vat_percentage')) ? $r->vat_percentage : auth()->user()->Store->vat_percentage;
        $ProductData['shipping'] = ($r->has('shipping')) ? $r->shipping : auth()->user()->Store->shipping;
        $Product = Product::create($ProductData);
        return $this->api_response($Product, true, 201);
    }

    public function postEdit(Request $r, Product $Product){
        // Check if the user is allowed to edit this product
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to edit this product', false, 403);
        }
        // Validate the request
        $Validator = $this->validateRequest($r->all(), 'update');
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        // All seems to be clear, do the update
        $Product->update($r->all());
        return $this->api_response($Product, true, 200);
    }

    public function delete(Product $Product){
        // Check if the user is allowed to delete this product
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to delete this product', false, 403);
        }
        $Product->delete();
        return $this->api_response('Product deleted successfully', true, 200);
    }
}
