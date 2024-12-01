<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function home(){
        return view('home');
    }

    public function payment(Request $request)
    {
        $tran_id = $request->txn_id;
        $currency = "BDT"; // aamarPay supports Two types of currency USD & BDT
        $amount = number_format($request->amount, 2);

        // For live Store Id & Signature Key please mail to support@aamarpay.com
        $store_id = "aamarpaytest";
        $signature_key = "dbb74894e82415a2f7ff0ec3a97e4183";
        $url = "https://sandbox.aamarpay.com/jsonpost.php"; // for Live Transaction use "https://secure.aamarpay.com/jsonpost.php"
        $success = url('/success');
        $fail = url('/fail');
        $cancel = url('/cancel');

        $postData = json_encode([
            "store_id" => $store_id,
            "tran_id" => $tran_id,
            "success_url" => $success,
            "fail_url" => $fail,
            "cancel_url" => $cancel,
            "amount" => $amount,
            "currency" => $currency,
            "signature_key" => $signature_key,
            "desc" => "Merchant Registration Payment",
            "cus_name" => "Asib",
            "cus_email" => "asib.uucse@gmail.com",
            "cus_add1" => "Test",
            "cus_add2" => "None",
            "cus_city" => "Dhaka",
            "cus_state" => "Dhaka",
            "cus_postcode" => "1206",
            "cus_country" => "Bangladesh",
            "cus_phone" => "+8801628044781",
            "type" => "json"
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false // Disable SSL verification
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error_msg], 500);
        }

        curl_close($curl);

        $responseObj = json_decode($response);

        if (isset($responseObj->payment_url) && !empty($responseObj->payment_url)) {
            $paymentUrl = $responseObj->payment_url;
            return redirect()->away($paymentUrl);
        } else {
            return response()->json(['error' => 'Payment URL not found in response'], 500);
        }
    }


    public function success(Request $request)
    {
        $request_id = $request->mer_txnid;
        $url = "http://sandbox.aamarpay.com/api/v1/trxcheck/request.php?request_id=$request_id&store_id=aamarpaytest&signature_key=dbb74894e82415a2f7ff0ec3a97e4183&type=json";

        $ch = curl_init();

        // Set the URL and other options for the cURL session
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Timeout after 30 seconds
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $error_msg], 500);
        }

        curl_close($ch);

        // Return the response from the API
        return response($response, 200)->header('Content-Type', 'application/json');
    }



    public function ipn(Request $request)
    {
        $request_id = $request->mer_txnid;
        $url = "http://sandbox.aamarpay.com/api/v1/trxcheck/request.php?request_id=$request_id&store_id=aamarpaytest&signature_key=dbb74894e82415a2f7ff0ec3a97e4183&type=json";

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,  // Timeout after 30 seconds
            CURLOPT_FOLLOWLOCATION => true,  // Follow redirects
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $error_msg], 500);
        }

        curl_close($ch);

        return response($response, 200)
                    ->header('Content-Type', 'application/json');
    }


    public function fail(Request $request){
        dd($request);
    }
    public function cancel(Request $request){
        dd($request);
    }
}
