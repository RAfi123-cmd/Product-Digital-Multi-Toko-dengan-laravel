<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request){
        Log::info('Midtrans Callback Received', $request->all());

        $serverKey = config('midtrans.serverKey');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if($hashedKey !== $request->signature_key){
            Log::error('Invalid signature key', [
                'received' => $request->signature_key,
                'calculated' => $hashedKey
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;
        Log::info('Processing transaction', [
            'orderId' => $orderId,
            'status' => $transactionStatus
        ]);

        $transaction = Transaction::where('code', $orderId)->first();

        if(!$transaction){
            Log::error('Transaction not found', ['orderId' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        Log::info('Found Transaction', [
            'id' => $transaction->id,
            'orderId' => $transaction->code,
            'current_status' => $transaction->status
        ]);

        switch ($transactionStatus) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $transaction->update(['status' => 'pending']);
                        Log::info('Updated to pending (capture/challenge)');
                    } else{
                        $transaction->update(['status' => 'success']);
                        Log::info('Updated to success (capture)');
                    }
                }
                break;
            case 'settlement':
                $transaction->update(['status' => 'success']);
                Log::info('Updated to success (settlement)');
                break;
            case 'pending':
                $transaction->update(['status' => 'pending']);
                Log::info('Updated to pending');
                break;
            case 'deny':
                $transaction->update(['status' => 'failed']);
                Log::info('Updated to failed (deny)');
                break;
            case 'expire':
                $transaction->update(['status' => 'failed']);
                Log::info('Updated to failed (expire)');
                break;
            case 'cancel':
                $transaction->update(['status' => 'failed']);
                Log::info('Updated to failed (cancel)');
                break;
            default:
                $transaction->update(['status' => 'failed']);
                Log::info('Updated to failed (default)');
                break;
        }

        Log::info('Transaction updated', ['id' => $transaction->id, 'status' => $transaction->status]);

        return response()->json(['message' => 'Callback received successfully']);
    }
}
