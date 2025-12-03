<div id="aiTranslatePopup" style="
    position: absolute;
    z-index: 2147483647;
    background: #fff;
    border: 2px solid #007bff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    min-width: 300px;
    max-width: 550px;
    display: none;
    opacity: 0;
    transition: opacity 0.2s ease;
">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border-bottom:1px solid #eee; background:#f8f9fa;">
        <div style="display:flex; align-items:center; gap:6px;">
            <select id="aiSrcLang" style="padding:5px 8px; font-size:12px;">
                <option value="vi" selected>Việt</option>
                <option value="en">Anh</option>
            </select>
            <button id="aiSwapBtn" type="button" style="padding:3px 8px; border:1px solid #007bff; background:white; cursor:pointer;">⇄</button>
            <select id="aiTgtLang" style="padding:5px 8px; font-size:12px;">
                <option value="en" selected>Anh</option>
                <option value="vi">Việt</option>
            </select>
            <button id="aiTranslateNowBtn" type="button" style="padding:3px 8px; border:1px solid #28a745; background:white; cursor:pointer; font-size:12px;">Dịch ngay</button>
        </div>
        <button id="aiCloseBtn" type="button" style="border:none; background:none; color:#dc3545; font-size:18px; cursor:pointer;">×</button>
    </div>
    <div id="aiTranslateResult" style="padding:10px; font-size:15px; line-height:1.5; color:#333;">
        Dịch tự động...
    </div>
</div>
<script src="{{ asset('clients/assets/js/ai-widget.js') }}"></script>
