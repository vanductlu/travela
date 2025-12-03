<h2>Xin chào {{ $booking->fullName }}</h2>

<p>Cảm ơn bạn đã đặt tour tại <strong>Travela</strong>.</p>

<p><strong>Tên tour:</strong> {{ $booking->title }}</p>
<p><strong>Mã booking:</strong> {{ $booking->bookingId }}</p>
<p><strong>Tổng tiền:</strong> {{ number_format($booking->totalPrice) }} VNĐ</p>
<p><strong>Ngày khởi hành:</strong> {{ date('d-m-Y', strtotime($booking->startDate)) }}</p>

<p>Chúng tôi sẽ liên hệ lại với bạn sớm nhất có thể.</p>

<p>Trân trọng,<br>Đội ngũ Travela</p>
