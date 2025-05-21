<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function cart(Request $request){
        $store = User::where('username', $request->username)->first();

        if(!$store){
            abort(404);
        }

        return view('pages.cart', compact('store'));
    }

    public function customerInformation(Request $request){
        $store = User::where('username', $request->username)->first();

        if(!$store){
            abort(404);
        }

        return view('pages.customer-information', compact('store'));
    }

    public function checkout(Request $request){
        $store = User::where('username', $request->username)->first();

        if(!$store){
            abort(404);
        }

        $carts = json_decode($request->cart, true);

        $totalPrice = 0;

        foreach ($carts as $cart) {
            $product = Product::where('id', $cart['id'])->first();
            $totalPrice += $product->price * $cart['qty'];
        }

        $transaction = $store->transactions()->create([
            'code' => 'TRX-' . mt_rand(10000, 99999),
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'table_number' => $request->table_number,
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        foreach ($carts as $cart) {
            $product = Product::where('id', $cart['id'])->first();

            $transaction->transactionDetails()->create([
                'product_id' => $product->id,
                'quantity' => $cart['qty'],
                'note' => $cart['notes'],
            ]);
        }

        if ($request->payment_method == 'cash') {
            return redirect()->route('success', ['username' => $store->username, 'order_id' => $transaction->code]);
        }
    }

    public function success(Request $request){
        $transaction = Transaction::where('code', $request->order_id)->first();
        $store = User::where('id', $transaction->user_id)->first();

        if(!$store){
            abort(404);
        }

        return view('pages.success', compact('store', 'transaction'));
    }
}
