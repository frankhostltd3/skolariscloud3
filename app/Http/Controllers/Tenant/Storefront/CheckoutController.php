<?php

namespace App\Http\Controllers\Tenant\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Pamphlet;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceipt;
use App\Mail\AdminNewOrder;

class CheckoutController extends Controller
{
    public function buyBook(Book $book): View
    {
        abort_if(!$book->is_published, 404);
        return view('tenant.storefront.checkout', [
            'type' => 'book',
            'item' => $book,
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function buyPamphlet(Pamphlet $pamphlet): View
    {
        abort_if(!$pamphlet->is_published, 404);
        return view('tenant.storefront.checkout', [
            'type' => 'pamphlet',
            'item' => $pamphlet,
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function purchaseBook(Book $book): RedirectResponse
    {
        abort_if(!$book->is_published, 404);
        $data = request()->only(['buyer_name','buyer_email','payment_method']);
        Validator::make($data, [
            'buyer_name' => ['required','string','max:255'],
            'buyer_email' => ['required','email','max:255'],
            'payment_method' => ['nullable','string'],
        ])->validate();
        $order = Order::create([
            'item_type' => 'book',
            'item_id' => $book->id,
            'item_title' => $book->title,
            'price' => $book->price,
            'buyer_name' => $data['buyer_name'],
            'buyer_email' => $data['buyer_email'],
            'status' => 'pending',
            'payment_method' => $data['payment_method'] ?? null,
        ]);
    $this->sendReceipt($order);
    $this->notifyAdmin($order);
        return redirect()->route('tenant.storefront.thanks')->with('success', __('Your order was placed.'));
    }

    public function purchasePamphlet(Pamphlet $pamphlet): RedirectResponse
    {
        abort_if(!$pamphlet->is_published, 404);
        $data = request()->only(['buyer_name','buyer_email','payment_method']);
        Validator::make($data, [
            'buyer_name' => ['required','string','max:255'],
            'buyer_email' => ['required','email','max:255'],
            'payment_method' => ['nullable','string'],
        ])->validate();
        $order = Order::create([
            'item_type' => 'pamphlet',
            'item_id' => $pamphlet->id,
            'item_title' => $pamphlet->title,
            'price' => $pamphlet->price,
            'buyer_name' => $data['buyer_name'],
            'buyer_email' => $data['buyer_email'],
            'status' => 'pending',
            'payment_method' => $data['payment_method'] ?? null,
        ]);
    $this->sendReceipt($order);
    $this->notifyAdmin($order);
        return redirect()->route('tenant.storefront.thanks')->with('success', __('Your order was placed.'));
    }

    public function thanks(): View
    {
        return view('tenant.storefront.thanks');
    }

    private function paymentMethods(): array
    {
        $locale = app()->getLocale();
        $map = Config::get('skolaris.payments.by_locale', []);
        $labels = Config::get('skolaris.payments.labels', []);
        $methods = $map[$locale] ?? Config::get('skolaris.payments.default', ['stripe','paypal']);
        return array_map(fn($key) => ['key' => $key, 'label' => $labels[$key] ?? ucfirst($key)], $methods);
    }

    private function sendReceipt(Order $order): void
    {
        try {
            Mail::to($order->buyer_email)->send(new OrderReceipt($order));
            $order->forceFill(['receipt_email_sent_at' => now()])->save();
        } catch (\Throwable $e) {
            // swallow mail errors for now
        }
    }

    private function notifyAdmin(Order $order): void
    {
        $to = Config::get('skolaris.notifications.orders.recipient');
        if (!$to) return;
        try {
            Mail::to($to)->send(new AdminNewOrder($order));
        } catch (\Throwable $e) {
            // swallow for now
        }
    }
}
