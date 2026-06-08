<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentNotificationController extends Controller
{
    /**
     * Menangani data callback otomatis (webhook) dari server Midtrans
     */
    public function handleNotification(Request $request)
    {
        // 1. Set konfigurasi Server Key dari file config/midtrans.php
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            // 2. Tangkap & validasi data notifikasi (Otomatis mencocokkan signature key)
            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $orderNumber = $notification->order_id; // Mengambil kode unik INV-XXXXXXXX
            $paymentType = $notification->payment_type;

            // 3. Cari data pesanan di database berdasarkan nomor invoice
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('Midtrans Webhook Warning: Invoice #' . $orderNumber . ' tidak ditemukan di database.');
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // 4. Logika Alur Perubahan Status Berdasarkan Status Transaksi Midtrans
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                
                // UANG MASUK & SUKSES! Ubah status jadi 'Diproses' agar masuk antrean dapur kasir
                $order->status = 'Diproses'; 
                $order->save();
                
                Log::info('Midtrans Webhook SUCCESS: Invoice #' . $orderNumber . ' lunas via ' . $paymentType);

            } elseif ($transactionStatus == 'pending') {
                
                // Pembeli baru memunculkan QRIS tapi belum bayar
                $order->status = 'Pending';
                $order->save();

            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                
                // Transaksi gagal, kedaluwarsa, atau dibatalkan oleh pembeli
                $order->status = 'Batal'; 
                $order->save();
                
                Log::info('Midtrans Webhook FAILED: Invoice #' . $orderNumber . ' berstatus ' . $transactionStatus);
            }

            // Beri respons 200 OK ke server Midtrans agar mereka berhenti menembak webhook ini
            return response()->json(['message' => 'Webhook Midtrans berhasil diproses'], 200);

        } catch (Exception $e) {
            // Jika ada kode yang crash, catat detail error-nya di file storage/logs/laravel.log
            Log::error('Midtrans Webhook Crash Error 500: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Webhook Error 500 Internal Server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}