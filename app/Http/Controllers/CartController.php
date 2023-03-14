<?php

namespace App\Http\Controllers;

use App\CartItem;
use App\Product;
use App\Store;
use Illuminate\Http\Request;

class CartController extends Controller {
    public function getAll(){
        $CartItems = CartItem::active()->where('user_id', auth()->user()->id)->get();
        $CartSubTotal = $CartItems->sum(function($Item){
            return $Item->qty * $Item->Product->item_price;
        });
        $VatTotal = $CartItems->sum(function($Item){
            return $Item->qty * $Item->Product->vat_value;
        });
        $ShippingTotal = 0;
        // Generate the total shipping based on store(s) settings
        $Stores = $CartItems->pluck('store_id')->unique();
        foreach($Stores as $Store){
            $ShippingTotal += Store::find($Store)->shipping;
        }
        return $this->api_response([
            'sub_total' => $CartSubTotal,
            'total' => $CartSubTotal + $ShippingTotal + $VatTotal,
            'total_shipping' => $ShippingTotal,
            'total_vat' => $VatTotal,
            'items_count' => $CartItems->sum('qty'),
            'items' => $CartItems
        ], true, 200);
    }
    public function postNew(Request $r){
        // Ensure there is a product_id in the request
        if(!$r->has('product_id')){
            return $this->api_response('You have to provide a product id', false, 422);
        }
        // Ensure there is such product
        $Product = Product::find($r->product_id);
        if(!$Product){
            return $this->api_response('There is no such product!', false, 404);
        }
        // Create or Update the cart item record
        $CartItem = CartItem::where('user_id', auth()->user()->id)->where('product_id', $r->product_id)->where('status', 'active')->first();
        if($CartItem){
            // There is a record, we just need to update the qty
            $CartItem->increment('qty', $r->qty ?? 1);
        }else{
            // This is a new one
            $CartItem = CartItem::create([
                'user_id' => auth()->user()->id,
                'product_id' => $Product->id,
                'store_id' => $Product->Store->id,
                'qty' => $r->qty ?? 1
            ]);
        }
        return $this->api_response($CartItem, true, 201);
    }

    public function delete(Request $r){
        // TODO: Handle qty while deleting the product
        if(!$r->has('cart_item_id')){
            return $this->api_response('You have to provide a cart item id', false, 422);
        }
        $CartItem = CartItem::find($r->cart_item_id);
        if($CartItem->user_id != auth()->user()->id){
            return $this->api_response('Your are not allowed to delete this record!', false, 403);
        }
        $CartItem->update([
            'status' => 'deleted'
        ]);
    }

}
