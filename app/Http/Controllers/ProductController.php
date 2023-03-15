<?php

namespace App\Http\Controllers;

use App\ProductLocale;
use Illuminate\Http\Request;

use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller {
    /**
     * @param (Request) $r
     * @param (string) $method
     * @description A validation helper to avoid repeating the same logic in each method below
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateRequest(Request $r, string $method): \Illuminate\Contracts\Validation\Validator {
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
        return Validator::make($r->all(), $Rules);
    }

    /**
     * @param (Request) $r
     * @param (array) $ProductData
     * @description Calculates the VAT percentage field based on the user input
     * @return array
     */
    private function calculateVatPercentage(Request $r, array $ProductData): array {
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

    /**
     * @description Return all the user's products utlizing the mine scope defined in the model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages GET /api/v1/product
     */
    public function getAll(){
        // Here, we can utilize a quick header check to know if we want to return the localized values or not
        // something like if ($r->header('X-LANGUAGE') == 'AR') { $Product->load('Locale'); } but I didn't do that for the sack of simplicity.
        return $this->api_response(Product::mine()->with('Locale')->get(), true, 200);
    }

    /**
     * @param Product $Product
     * @description Return a single product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages GET /api/v1/product/{id}
     */
    public function getSingle(Product $Product){
        // Ensure the product is owned by the current user
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to view this product', false, 403);
        }
        // Here, we can utilize a quick header check to know if we want to return the localized values or not
        // something like if ($r->header('X-LANGUAGE') == 'AR') { $Product->load('Locale'); } but I didn't do that for the sack of simplicity.
        $Product->load('Locale');
        return $this->api_response($Product, true, 200);
    }

    /**
     * @param Request $r
     * @description Create a new product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages POST /api/v1/product
     */
    public function postNew(Request $r){
        // Validate the request
        $Validator = $this->validateRequest($r, 'create');
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        // Ensure the user has already created a store
        if(!auth()->user()->hasStore()){
            return $this->api_response('You need to create a store first!', false, 422);
        }
        $ProductData = $r->except('vat_type');
        $ProductData['user_id'] = auth()->user()->id;
        $ProductData['store_id'] = auth()->user()->Store->id;
        $ProductData = $this->calculateVatPercentage($r, $ProductData);

        $Product = Product::create($ProductData);
        return $this->api_response($Product, true, 201);
    }

    /**
     * @param Request $r
     * @param Product $Product
     * @description Update a single product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages POST /api/v1/product/{id}
     */
    public function postEdit(Request $r, Product $Product){
        // Check if the user is allowed to edit this product
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to edit this product', false, 403);
        }
        // Validate the request
        $Validator = $this->validateRequest($r, 'update');
        if($Validator->fails()){
            return $this->api_response($Validator->errors(), false, 422);
        }
        // All seems to be clear, do the update
        $ProductData = $r->all();
        $ProductData = $this->calculateVatPercentage($r, $ProductData);
        $Product->update($ProductData);
        return $this->api_response($Product, true, 200);
    }

    /**
     * @param Product $Product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @description Delete a product
     * @usages DELETE /api/v1/product/{id}
     */
    public function delete(Product $Product){
        // Check if the user is allowed to delete this product
        if(auth()->user()->id != $Product->user_id){
            return $this->api_response('You are not allowed to delete this product', false, 403);
        }
        $Product->delete();
        return $this->api_response('Product deleted successfully', true, 200);
    }

    /**
     * @param Request $r
     * @param Product $Product
     * @description Update or Create the translated data for a product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages POST /api/v1/product/localize/{id}
     */

    public function postLocalize(Request $r, Product $Product){
        $Validator = $this->validateRequest($r, 'localize');
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
