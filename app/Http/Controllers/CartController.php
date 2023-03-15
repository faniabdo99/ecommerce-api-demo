<?php

namespace App\Http\Controllers;

use App\CartItem;
use App\Product;
use App\Store;
use Illuminate\Http\Request;

class CartController extends Controller {

    /**
     * @description Returns a list of the cart items for the user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages GET /api/v1/cart
     */
    public function getAll(){
        // Get a list of the active cart items for the current user
        $CartItems = CartItem::active()->where('user_id', auth()->user()->id)->get();
        // Generate the cart subtotal
        $CartSubTotal = $CartItems->sum(function($Item){
            return $Item->qty * $Item->Product->item_price;
        });
        // Generate the VAT total
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

    /**
     * @param Request $r
     * @description Add a new item to the cart
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @usages POST /api/v1/cart/add
     */
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

    /**
     * @param Request $r
     * @description Delete a cart item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
     */
    public function delete(Request $r, CartItem $CartItem){
        // Ensure the cart_item_id actually belongs to the user
        if($CartItem->user_id != auth()->user()->id){
            return $this->api_response('Your are not allowed to delete this record!', false, 403);
        }
        // Ensure the qty field has a value
        $r->qty = ( !$r->has('qty') ) ? 1 : $r->qty;
        if($CartItem->qty <= $r->qty){
            // We don't delete the record, the deleted carts can be a valuable information for the marketing team to detect patterns & identify any issues (Abandoned Carts)
            $CartItem->update([
                'status' => 'deleted'
            ]);
            return $this->api_response('Cart item has been deleted', true, 200);
        }else{
            $CartItem->decrement('qty', intval($r->qty));
            return $this->api_response(['message' => 'Cart Item has been updated', $CartItem], true, 200);
        }
    }

}
