<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Notifications\NewOrderNotification;
use App\Order;
use App\OrderProduct;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrdersController extends Controller
{
    public function index()
    {
        return view('orders', [
            'orders' => Auth::user()->orders,
        ]);
    }

    public function checkout()
    {
        $user = Auth::user();
        $products = $user->cartProducts;
        if (!$products) {
            return redirect()->route('home');
        }

        DB::beginTransaction();
        try {

            $order = $user->orders()->create([
                'status' => 'pending',
            ]);
            foreach ($products as $product) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $product->cart->quantity,
                ]);
            }

            //Cart::where('user_id', $user->id)->delete();

            // Notify the Admin
            $admin = User::where('type', 'super-admin')->first();
            $admin->notify(new NewOrderNotification);

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
            return redirect().route('cart.index');
        }

        return redirect()->route('cart.index');
    }
}
