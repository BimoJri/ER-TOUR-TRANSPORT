<?php
namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function generateSnapToken($order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone ?? '',
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function handleNotification($payload)
    {
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;
        $order = Order::find($order_id);

        if ($transaction == 'capture') {
            if ($notif->payment_type == 'credit_card') {
                $order->status = 'paid';
                $order->save();
            }
        } elseif ($transaction == 'settlement') {
            $order->status = 'paid';
            $order->save();
        } elseif ($transaction == 'pending') {
            $order->status = 'pending';
            $order->save();
        } elseif ($transaction == 'deny') {
            $order->status = 'failed';
            $order->save();
        } elseif ($transaction == 'expire') {
            $order->status = 'expired';
            $order->save();
        } elseif ($transaction == 'cancel') {
            $order->status = 'cancelled';
            $order->save();
        }

        return $order;
    }
}