<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-row { margin: 10px 0; padding: 10px; background: white; }
        .label { font-weight: bold; color: #4CAF50; }
        .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Thông báo liên hệ mới</h2>
        </div>
        <div class="content">
            <p>Bạn có một liên hệ mới từ website Travela:</p>
            
            <div class="info-row">
                <span class="label">👤 Họ và tên:</span> {{ $contactData['fullName'] }}
            </div>
            
            <div class="info-row">
                <span class="label">📧 Email:</span> 
                <a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a>
            </div>
            
            <div class="info-row">
                <span class="label">📞 Số điện thoại:</span> 
                <a href="tel:{{ $contactData['phoneNumber'] }}">{{ $contactData['phoneNumber'] }}</a>
            </div>
            
            <div class="info-row">
                <span class="label">💬 Nội dung:</span><br>
                <div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-left: 3px solid #4CAF50;">
                    {{ $contactData['message'] }}
                </div>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 3px solid #ffc107;">
                <strong>⚠️ Lưu ý:</strong> Vui lòng phản hồi khách hàng trong vòng 24 giờ!
            </div>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống Travela</p>
            <p>© {{ date('Y') }} Travela. All rights reserved.</p>
        </div>
    </div>
</body>
</html>