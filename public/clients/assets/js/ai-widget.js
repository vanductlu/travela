(function () {
    let popup, result, srcSelect, tgtSelect, swapBtn, closeBtn, translateNowBtn;
    let active = true;
    let originalText = '';
    let lastTranslation = '';
    let isTranslating = false;
    let suppressSelectEvents = false;

    function showPopup(x, y, text) {
        if (!active) return;
        popup.style.display = 'block';
        popup.style.opacity = '1';
        popup.style.left = x + 'px';
        popup.style.top = y + 'px';
        originalText = text;
        lastTranslation = '';
        translate(originalText);
    }

    function hidePopup() {
        popup.style.display = 'none';
        popup.style.opacity = '0';
    }

    async function translate(text) {
        if (!text || isTranslating) return;
        isTranslating = true;
        result.textContent = 'Đang dịch...';
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
            const translated = data.translation || 'Không thể dịch';
            result.textContent = translated;
            lastTranslation = (translated === 'Không thể dịch') ? '' : translated;
        } catch (err) {
            result.textContent = 'Lỗi: ' + err.message;
            lastTranslation = '';
        } finally {
            isTranslating = false;
        }
    }

    function swapLanguages() {
        const tmp = srcSelect.value;
        srcSelect.value = tgtSelect.value;
        tgtSelect.value = tmp;
        if (lastTranslation) {
            originalText = lastTranslation;
            translate(originalText);
        } else if (originalText) {
            translate(originalText);
        }
    }

    function init() {
        popup = document.getElementById('aiTranslatePopup');
        result = document.getElementById('aiTranslateResult');
        srcSelect = document.getElementById('aiSrcLang');
        tgtSelect = document.getElementById('aiTgtLang');
        swapBtn = document.getElementById('aiSwapBtn');
        closeBtn = document.getElementById('aiCloseBtn');
        translateNowBtn = document.getElementById('aiTranslateNowBtn');

        swapBtn.addEventListener('click', swapLanguages);
        closeBtn.addEventListener('click', hidePopup);

        translateNowBtn.addEventListener('click', () => {
            translate(originalText);
        });

        srcSelect.addEventListener('change', () => {
            if (suppressSelectEvents) return;
            suppressSelectEvents = true;
            tgtSelect.value = (srcSelect.value === 'vi') ? 'en' : 'vi';
            suppressSelectEvents = false;
        });

        tgtSelect.addEventListener('change', () => {
            if (suppressSelectEvents) return;
        });

        document.addEventListener('mouseup', (e) => {
            if (!active) return;
            if (popup.contains(e.target)) return;
            const sel = window.getSelection();
            const text = sel.toString().trim();
            if (text.length === 0) {
                hidePopup();
                return;
            }
            const range = sel.rangeCount ? sel.getRangeAt(0) : null;
            if (!range) return hidePopup();
            const rect = range.getBoundingClientRect();
            showPopup(rect.left + window.scrollX + 10, rect.bottom + window.scrollY + 10, text);
        });

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
