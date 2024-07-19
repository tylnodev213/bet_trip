<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VNPayPayment
{
    public static function purchase(array $options = [])
    {
        $vnp_TmnCode = env('VNPAY_CODE'); //Mã định danh merchant kết nối (Terminal Id)
        $vnp_HashSecret = env('VNPAY_KEY'); //Secret key
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = $options['redirectUrl'];
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
        $vnp_Amount = $options['amount']; // Số tiền thanh toán
        $vnp_Locale = 'vn'; //Ngôn ngữ chuyển hướng thanh toán
        $vnp_BankCode = ''; //Mã phương thức thanh toán
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_BankCode" => "VNBANK",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $options['orderInfo'],
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $options['orderId'],
            "vnp_ExpireDate" => $expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    public static function completePurchase(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_KEY');
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $success = false;
        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $resultMessage = array('Thanh toán thành công');
                $success = true;
            } else {
                $resultMessage = 'GD Khong thanh cong';
            }
        } else {
            $resultMessage = 'Chữ ký không hợp lệ';
        }

        return array('success' => $success, 'message' => $resultMessage);
    }

    public static function refund(array $options = [])
    {
        $vnp_TmnCode = env('VNPAY_CODE'); //Mã định danh merchant kết nối (Terminal Id)
        $vnp_HashSecret = env('VNPAY_KEY'); //Secret key
        $refundUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
        $data = [
            "vnp_RequestId" => $_SERVER['REMOTE_ADDR'],
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "refund",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_TransactionType" => '03',
            "vnp_TxnRef" => $options['orderId'],
            "vnp_Amount" => $options['amountRefund'],
            "vnp_TransactionNo" => $options['tranNo'],
            "vnp_TransactionDate" => $options['tranDate'],
            "vnp_CreateBy" => $options['userName'],
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
            "vnp_OrderInfo" => 'Hoan tien huy booking GoodTrip',
        ];

        ksort($data);
        $hashdata = http_build_query($data, null, '|', PHP_QUERY_RFC3986);
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $data['vnp_SecureHash'] = $vnpSecureHash;
        }

        return Http::post($refundUrl, $data);
    }
}

