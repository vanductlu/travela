// Lấy các elements
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");
const chatbox = document.querySelector(".chat-body");
const chatInput = document.querySelector(".message-input");
const sendChatBtn = document.querySelector("#send-message");
const chatForm = document.querySelector(".chat-form");

let userMessage = null;
let isProcessing = false; // Cờ để chặn gửi nhiều tin nhắn cùng lúc

// Lấy CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Kiểm tra CSRF token
if (!csrfToken) {
    console.error('❌ CSRF token not found!');
}

console.log('✅ CSRF Token:', csrfToken);
console.log('✅ Current domain:', window.location.hostname);
console.log('✅ Cookies:', document.cookie);

// Tạo tin nhắn chat
const createChatLi = (message, className) => {
    const chatLi = document.createElement("div");
    chatLi.classList.add("message", className);
    
    let chatContent = className === "bot-message" 
        ? `<svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
            <path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"/>
          </svg>
          <div class="message-text"></div>` 
        : `<div class="message-text"></div>`;
    
    chatLi.innerHTML = chatContent;
    chatLi.querySelector(".message-text").textContent = message;
    return chatLi;
}

// Gửi tin nhắn tới server
const generateResponse = (chatElement) => {
    const messageElement = chatElement.querySelector(".message-text");
    
    console.log('📤 Sending message:', userMessage);
    
    // Kiểm tra CSRF token trước khi gửi
    if (!csrfToken) {
        messageElement.textContent = 'Lỗi: Không tìm thấy CSRF token. Vui lòng tải lại trang.';
        messageElement.classList.add("error");
        return;
    }
    
    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest' // Thêm header này
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            message: userMessage
        })
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        console.log('📥 Response ok:', response.ok);
        
        // Log headers để debug
        response.headers.forEach((value, key) => {
            console.log(`Header ${key}: ${value}`);
        });
        
        // Xử lý lỗi 401
        if (response.status === 401) {
            messageElement.textContent = '⚠️ Bạn chưa đăng nhập. Vui lòng đăng nhập để sử dụng chatbot.';
            messageElement.classList.add("error");
            
            // Hiển thị confirm dialog sau 1 giây
            setTimeout(() => {
                if (confirm('Bạn chưa đăng nhập. Chuyển đến trang đăng nhập?')) {
                    window.location.href = '/login';
                }
            }, 1000);
            
            throw new Error('Unauthorized');
        }
        
        // Xử lý các lỗi HTTP khác
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }).catch(err => {
                throw new Error('Có lỗi xảy ra khi gửi tin nhắn');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('📥 Response data:', data);
        
        if (data.success) {
            messageElement.textContent = data.reply;
            messageElement.classList.remove("error");
        } else {
            messageElement.textContent = data.reply || data.message || 'Có lỗi xảy ra.';
            messageElement.classList.add("error");
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        
        // Không ghi đè message nếu đã có error message
        if (!messageElement.classList.contains("error")) {
            messageElement.textContent = error.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
            messageElement.classList.add("error");
        }
    })
    .finally(() => {
        chatbox.scrollTo(0, chatbox.scrollHeight);
        
        // Mở khóa input sau khi hoàn thành
        isProcessing = false;
        chatInput.disabled = false;
        sendChatBtn.disabled = false;
        chatInput.focus(); // Focus lại vào input
    });
}

// Xử lý chat
const handleChat = () => {
    userMessage = chatInput.value.trim();
    if (!userMessage) return;
    
    // Chặn nếu đang xử lý request khác
    if (isProcessing) {
        console.warn('Đang xử lý tin nhắn trước, vui lòng đợi...');
        return;
    }

    isProcessing = true; // Đánh dấu đang xử lý
    
    // Disable input khi đang gửi
    chatInput.disabled = true;
    sendChatBtn.disabled = true;

    // Xóa input
    chatInput.value = "";
    chatInput.style.height = "auto";

    // Thêm tin nhắn user vào chatbox
    chatbox.appendChild(createChatLi(userMessage, "user-message"));
    chatbox.scrollTo(0, chatbox.scrollHeight);

    setTimeout(() => {
        // Hiển thị "Đang suy nghĩ..."
        const incomingChatLi = createChatLi("Đang suy nghĩ...", "bot-message");
        chatbox.appendChild(incomingChatLi);
        chatbox.scrollTo(0, chatbox.scrollHeight);
        generateResponse(incomingChatLi);
    }, 600);
}

// Tự động resize textarea
chatInput.addEventListener("input", () => {
    chatInput.style.height = "auto";
    chatInput.style.height = `${chatInput.scrollHeight}px`;
});

// Gửi tin nhắn khi nhấn Enter (không shift)
chatInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        handleChat();
    }
});

// Submit form
chatForm.addEventListener("submit", (e) => {
    e.preventDefault();
    handleChat();
});

// Toggle chatbot
chatbotToggler.addEventListener("click", () => {
    document.body.classList.toggle("show-chatbot");
});

closeChatbot.addEventListener("click", () => {
    document.body.classList.remove("show-chatbot");
});

// Không test auth ngay khi load, sẽ test khi gửi message
console.log('✅ Chatbot loaded successfully');