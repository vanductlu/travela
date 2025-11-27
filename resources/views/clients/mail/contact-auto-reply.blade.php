<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 10px 10px; }
        .highlight { background: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; }
        .contact-info { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 30px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Cảm ơn bạn đã liên hệ!</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $customerName }}</strong>,</p>
            
            <p>Cảm ơn bạn đã gửi thông tin liên hệ đến <strong>Travela</strong>. Chúng tôi đã nhận được yêu cầu của bạn và sẽ phản hồi trong thời gian sớm nhất.</p>
            
            <div class="highlight">
                <strong>📋 Thông tin của bạn đã được ghi nhận</strong><br>
                Đội ngũ hỗ trợ của chúng tôi sẽ liên hệ lại với bạn trong vòng <strong>24 giờ</strong>.
            </div>
            
            <p>Trong thời gian chờ đợi, bạn có thể:</p>
            <ul>
                <li>🌍 Khám phá các <strong>tour du lịch hot</strong> trên website</li>
                <li>📱 Theo dõi chúng tôi trên mạng xã hội</li>
                <li>📞 Gọi hotline: <strong>0364869849</strong> nếu cần hỗ trợ khẩn cấp</li>
            </ul>
            
            <div class="contact-info">
                <h3 style="color: #4CAF50; margin-top: 0;">📞 Thông tin liên hệ</h3>
                <p><strong>Email:</strong> nvd2k3@gmail.com</p>
                <p><strong>Hotline:</strong> 0364869849</p>
                <p><strong>Địa chỉ:</strong> 173 Khâm Thiên, Quận Đống Đa, Hà Nội</p>
            </div>
            
            <center>
                <a href="http://127.0.0.1:8000" class="btn">Về trang chủ Travela</a>
            </center>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời trực tiếp.</p>
            <p>© {{ date('Y') }} Travela. All rights reserved.</p>
        </div>
    </div>
</body>
</html>