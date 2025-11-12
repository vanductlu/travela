(function () {
    let popup, result, srcSelect, tgtSelect, swapBtn, closeBtn, toggleBtn;
    let active = true;
    let currentText = '';
    let isTranslating = false;

    function showPopup(x, y, text) {
        if (!active) return;
        popup.style.display = 'block';
        popup.style.opacity = '1';
        popup.style.left = x + 'px';
        popup.style.top = y + 'px';
        translate(text);
    }

    function hidePopup() {
        popup.style.display = 'none';
        popup.style.opacity = '0';
    }

    async function translate(text) {
        if (!text || isTranslating) return;
        isTranslating = true;
        result.innerHTML = '⏳ Đang dịch...';
        try {
            const response = await fetch('http://127.0.0.1:5556/api/translate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: text.trim(),
                    src: srcSelect.value,
                    tgt: tgtSelect.value
                })
            });
            const data = await response.json();
            result.textContent = data.translation || '⚠️ Không thể dịch';
        } catch (err) {
            result.textContent = '❌ Lỗi: ' + err.message;
        } finally {
            isTranslating = false;
        }
    }

    function swapLanguages() {
        const tmp = srcSelect.value;
        srcSelect.value = tgtSelect.value;
        tgtSelect.value = tmp;
        if (currentText) translate(currentText);
    }

    function init() {
        popup = document.getElementById('aiTranslatePopup');
        result = document.getElementById('aiTranslateResult');
        srcSelect = document.getElementById('aiSrcLang');
        tgtSelect = document.getElementById('aiTgtLang');
        swapBtn = document.getElementById('aiSwapBtn');
        closeBtn = document.getElementById('aiCloseBtn');
        toggleBtn = document.getElementById('aiToggleBtn');

        // Bật/tắt toàn bộ AI dịch
        toggleBtn.addEventListener('click', () => {
            active = !active;
            if (active) {
                toggleBtn.style.background = '#007bff';
                toggleBtn.textContent = '🌐';
            } else {
                hidePopup();
                toggleBtn.style.background = '#6c757d';
                toggleBtn.textContent = '🚫';
            }
        });

        // Sự kiện swap & close
        swapBtn.addEventListener('click', swapLanguages);
        closeBtn.addEventListener('click', hidePopup);

        // Khi đổi ngôn ngữ thì dịch lại
        srcSelect.addEventListener('change', () => {
            if (currentText) translate(currentText);
        });
        tgtSelect.addEventListener('change', () => {
            if (currentText) translate(currentText);
        });

        // Khi người dùng tô đen văn bản
        document.addEventListener('mouseup', (e) => {
            if (!active) return;
            if (popup.contains(e.target)) return;

            const sel = window.getSelection();
            const text = sel.toString().trim();
            if (text.length === 0) {
                hidePopup();
                return;
            }

            currentText = text;
            const range = sel.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            const x = rect.left + window.scrollX + 10;
            const y = rect.bottom + window.scrollY + 10;
            showPopup(x, y, text);
        });

        // Ẩn popup khi click ra ngoài
        document.addEventListener('click', (e) => {
            if (!popup.contains(e.target) && !window.getSelection().toString()) {
                hidePopup();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
