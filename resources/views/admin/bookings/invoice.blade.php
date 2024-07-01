<!doctype html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hóa đơn</title>

    <style type="text/css">
        * {
            font-family: "DejaVu Sans", sans-serif;
        }
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html,
        body {
            margin: 0;
            padding: 0;
        }
    </style>

</head>
<body>

<div style="max-width: 800px;margin: auto;padding: 16px;border: 1px solid #eee;font-size: 16px;line-height: 24px;font-family: 'Inter', sans-serif;color: #555;background-color: #F9FAFC;">
    <table style="font-size: 12px; line-height: 20px;">
        <thead>
        <tr>
            <td style="padding: 0 16px 18px 16px;">
                <h1 style="color: #1A1C21;font-size: 18px;font-style: normal;font-weight: 600;line-height: normal;">GoodTrip YourFriend</h1>
                <p>{{ config('config.email') }}</p>
                <p>{{ config('config.tel') }}</p>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <table style="background-color: #FFF; padding: 20px 16px; border: 1px solid #D7DAE0;width: 100%; border-radius: 12px;font-size: 12px; line-height: 20px; table-layout: fixed;">
                    <tbody>
                    <tr>
                        <td style="vertical-align: top; width: 30%; padding-right: 20px;padding-bottom: 35px;">
                            <p style="font-weight: 700; color: #1A1C21;">{{ $booking->customer->first_name . ' ' .  $booking->customer->last_name}}</p>
                            @php
                                $address = !empty($booking->customer->address) && !empty($booking->customer->province) && !empty($booking->customer->city) && !empty($booking->customer->country)
                                    ? sprintf("%s, %s, %s, %s", $booking->customer->address, $booking->customer->province, $booking->customer->city, $booking->customer->country)
                                    : '';
                            @endphp
                            @if(!empty($address))<p style="color: #5E6470;">{{ $address }}</p>@endif
                            <p style="color: #5E6470;">{{ $booking->customer->email }}</p>
                        </td>
                        <td style="vertical-align: top;padding-bottom: 35px;">

                        </td>
                        <td style="vertical-align: top;padding-bottom: 35px;">
                            <table style="table-layout: fixed;width:-webkit-fill-available;">
                                <tr>
                                    <th style="text-align: left; color: #1A1C21;">Ngày đặt:</th>
                                    <td style="text-align: right;">{{ date("Y-m-d",strtotime($booking->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left; color: #1A1C21;">Loại thanh toán :</th>
                                    <td style="text-align: right;">
                                        @if($booking->payment_method == PAYMENT_CASH)
                                            Tiền mặt
                                        @elseif($booking->payment_method == PAYMENT_VNPAY)
                                            VnPay
                                        @endif</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 13px;">
                            <p style="color: #5E6470;">Dịch vụ </p>
                            <p style="font-weight: 700; color: #1A1C21;">Booking tour</p>
                        </td>
                        <td style="text-align: center; padding-bottom: 13px;">
                            <p style="color: #5E6470;">Mã hóa đơn</p>
                            <p style="font-weight: 700; color: #1A1C21;">#{{ $booking->invoice_no }}</p>
                        </td>
                        <td style="text-align: end; padding-bottom: 13px;">
                            <p style="color: #5E6470;">Ngày xuất</p>
                            <p style="font-weight: 700; color: #1A1C21;">{{ date("Y-m-d H:i:s") }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table style="width: 100%;border-spacing: 0;">
                                <thead>
                                <tr style="text-transform: uppercase;">
                                    <td style="padding: 8px 0; border-block:1px solid #D7DAE0;">Dịch vụ</td>
                                    <td style="padding: 8px 0; border-block:1px solid #D7DAE0; text-align: end;">Số lượng</td>
                                    <td style="padding: 8px 0; border-block:1px solid #D7DAE0; text-align: end; width: 100px;">
                                        Giá</td>
                                    <td style="padding: 8px 0; border-block:1px solid #D7DAE0; text-align: end; width: 120px;">
                                        Tổng tiền</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="padding-block: 12px;">
                                        <p style="font-weight: 700; color: #1A1C21;">{{ $booking->tour->name }}</p>
                                        <p style="color: #5E6470;">{{ $booking->tour->type->name }}</p>
                                    </td>
                                    <td style="padding-block: 12px; text-align: end;">
                                        <p style="font-weight: 700; color: #1A1C21;">{{ $booking->people }}</p>
                                    </td>
                                    <td style="padding-block: 12px; text-align: end;">
                                        <p style="font-weight: 700; color: #1A1C21;">{{ number_format($booking->price) }}đ</p>
                                    </td>
                                    <td style="padding-block: 12px; text-align: end;">
                                        <p style="font-weight: 700; color: #1A1C21;">{{ number_format($booking->people * $booking->price) }}đ</p>
                                    </td>
                                </tr>
                                @foreach($booking->rooms as $room)
                                    <tr>
                                        <td style="padding-block: 12px;">
                                            <p style="font-weight: 700; color: #1A1C21;">{{ $room->name }}</p>
                                        </td>
                                        <td style="padding-block: 12px; text-align: end;">
                                            <p style="font-weight: 700; color: #1A1C21;">{{ $room->pivot->number }}</p>
                                        </td>
                                        <td style="padding-block: 12px; text-align: end;">
                                            <p style="font-weight: 700; color: #1A1C21;">{{ number_format($room->pivot->price) }}đ</p>
                                        </td>
                                        <td style="padding-block: 12px; text-align: end;">
                                            <p style="font-weight: 700; color: #1A1C21;">{{ number_format($room->pivot->number * $room->pivot->price) }}đ</p>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td style="padding: 12px 0; border-top:1px solid #D7DAE0;">
                                        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}">
                                    </td>
                                    <td style="border-top:1px solid #D7DAE0;" colspan="3">
                                        <table style="width: 100%;border-spacing: 0;">
                                            <tbody>
                                            <tr>
                                                <th style="padding-top: 12px;text-align: start; color: #1A1C21;">
                                                    Giá:</th>
                                                <td style="padding-top: 12px;text-align: end; color: #1A1C21;">
                                                    {{ number_format($booking->total / (100 - $booking->discount) * 100) }}₫</td>
                                            </tr>
                                            <tr>
                                                <th style="padding: 12px 0;text-align: start; color: #1A1C21;">
                                                    Giám giá:</th>
                                                <td style="padding: 12px 0;text-align: end; color: #1A1C21;">
                                                    {{ $booking->discount }}%</td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th style="padding: 12px 0 30px 0;text-align: start; color: #1A1C21;border-top:1px solid #D7DAE0;">
                                                    Tổng giá:</th>
                                                <th style="padding: 12px 0 30px 0;text-align: end; color: #1A1C21;border-top:1px solid #D7DAE0;">
                                                    {{ number_format($booking->total) }} ₫</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td style="padding-top: 30px;">
                <p style="display: flex; gap: 0 13px;">Cảm ơn đã đặt tour của chúng tôi !</p>
                <p>----------------------------------------------------</p>
                <p style="color: #1A1C21;">Hóa đơn được tạo bởi: GoodTrip Group</p>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    window.print();
</script>
</body>
</html>
