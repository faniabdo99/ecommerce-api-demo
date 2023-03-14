<?php

namespace App\Http\Controllers;

use App\ProductLocale;
use Illuminate\Http\Request;

use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller {
    private function validateRequest($r, $method){
        $Rules = [];
        switch ($method){
            case 'create':
                $Rules['title'] = 'required';
                $Rules['description'] = 'required';
                $Rules['price'] = 'required|integer';
                $Rules['vat_type'] = ['required_with:vat_percentage', Rule::in(['percentage', 'fixed'])];
                $Rules['vat_percentage'] = 'required_with:vat_type';
                break;
            case 'update':
                $Rules['vat_type'] = Rule::in(['percentage', 'fixed']);
                $Rules['vat'] = 'required_with:vat_type';
                break;
            case 'localize':
                $Rules['title'] = 'required';
                $Rules['description'] = 'required';
                break;
            default:
                $Rules['title'] = 'required';
                $Rules['description'] = 'required';
                $Rules['price'] = 'required|integer';
                $Rules['vat_type'] = ['required_with:vat_percentage', Rule::in(['percentage', 'fixed'])];
                $Rules['vat_percentage'] = 'required_with:vat_type';
        }
        return Validator::make($r, $Rules);
    }
    private function calculateVatPercentage($r, $ProductData){
        // Calculate the vat_percentage field
        if($r->has('vat_type') && $r->has('vat_percentage')){
            $ProductData['is_vat_included'] = true;
            if($r->vat_type == 'fixed'){
                // Calculate the VAT percentage
                $ProductData['vat_percentage'] = round($r->vat_percentage / $r->price * 100);
            }
        }else{
            // Default to the store vat_percentage, this means the client didn't send a vat_percentage so the VAT is included in the price
            // which means the VAT value for this product is what the store default is ...
            $ProductData['vat_percentage'] = auth()->user()->Store->vat_percentage;
            $ProductData['is_vat_included'] = false;
        }
        return $ProductData;
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
        $ProductData = $r->except('vat_type');
        $ProductData['user_id'] = auth()->user()->id;
        $ProductData['store_id'] = auth()->user()->Store->id;
        $ProductData = $this->calculateVatPercentage($r, $ProductData);

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
        $ProductData = $r->all();
        $ProductData = $this->calculateVatPercentage($r, $ProductData);
        $Product->update($ProductData);
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

    public function postLocalize(Request $r, Product $Product){
        $Validator = $this->validateRequest($r->all(), 'localize');
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        // Ensure the user is allowed to localize the product
        if($Product->user_id != auth()->user()->id){
            return $this->api_response('You are not allowed to change the translation on this product', false, 403);
        }
        // Create or Update the localization record
        ProductLocale::updateOrCreate(['product_id' => $Product->id], [
           'title_value' => $r->title,
           'description_value' => $r->description,
           'product_id' => $Product->id
        ]);
        return $this->api_response('The product has been translated to Arabic!', true, 201);
    }
}
