// resources/js/app.js

import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================================
    // MODULE CHATBOX CLIENT
    // ==========================================================
    const chatWidget = document.getElementById('chat-widget');
    const userIdMeta = document.querySelector('meta[name="user-id"]');

    if (chatWidget && userIdMeta) {
        
        const currentUserId = parseInt(userIdMeta.getAttribute('content'));
        const adminId = 1; // Giả định Admin luôn có ID là 1
        let historyLoaded = false;

        const messagesList = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-chat-btn');
        const chatHeader = document.getElementById('chat-header');
        const chatBody = document.getElementById('chat-body');
        
        // Chỉ chạy nếu tất cả các element cần thiết đều tồn tại
        if (messagesList && chatInput && sendBtn && chatHeader && chatBody) {
            
            const scrollToBottom = () => {
                chatBody.scrollTop = chatBody.scrollHeight;
            };

            const renderMessage = (message) => {
                const li = document.createElement('li');
                const senderName = message.sender_id === currentUserId ? 'Bạn' : (message.sender?.name || 'Admin');
                if (message.sender_id === currentUserId) {
                    li.classList.add('my-message');
                }
                li.innerHTML = `<strong>${senderName}:</strong> ${message.message}`;
                messagesList.appendChild(li);
            };

            const loadChatHistory = async () => {
                if (historyLoaded) return;
                try {
                    messagesList.innerHTML = '<li>Đang tải...</li>';
                    const response = await fetch(`/chat/history/${adminId}`);
                    if (!response.ok) throw new Error('Failed to load history');
                    const messages = await response.json();
                    
                    messagesList.innerHTML = '';
                    messages.forEach(renderMessage);
                    scrollToBottom();
                    historyLoaded = true;
                } catch (error) {
                    console.error('Lỗi khi tải lịch sử chat:', error);
                    messagesList.innerHTML = '<li>Không thể tải lịch sử.</li>';
                }
            };

            // Lắng nghe kênh riêng tư
            const participants = [currentUserId, adminId].sort((a, b) => a - b);
            const channelName = `chat.${participants[0]}.${participants[1]}`;
            
            if (window.Echo) {
                window.Echo.private(channelName)
                    .listen('.new-message', (e) => {
                        if (e.message.sender_id === adminId) { // Chỉ hiển thị tin nhắn từ admin
                            renderMessage(e.message);
                            scrollToBottom();
                        }
                    });
            }

            const sendMessage = () => {
                const messageText = chatInput.value;
                if (messageText.trim() === '') return;

                const tempMessage = { sender_id: currentUserId, message: messageText };
                renderMessage(tempMessage); // Hiển thị tạm tin nhắn của mình
                scrollToBottom();
                chatInput.value = '';

                fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: messageText, receiver_id: adminId })
                })
                .then(res => res.json())
                .catch(error => {
                    console.error('Lỗi khi gửi tin nhắn:', error);
                    const errorLi = document.createElement('li');
                    errorLi.innerText = 'Gửi thất bại.';
                    errorLi.style.color = 'red';
                    messagesList.appendChild(errorLi);
                });
            };

            // Gán sự kiện
            sendBtn.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', (e) => (e.key === 'Enter' ? sendMessage() : null));
            
            // Sự kiện toggle cửa sổ chat
            chatHeader.addEventListener('click', () => {
                chatWidget.classList.toggle('collapsed');
                if (!chatWidget.classList.contains('collapsed') && !historyLoaded) {
                    loadChatHistory();
                }
            });
        }
    }

    // ==========================================================
    // BẠN CÓ THỂ THÊM CÁC MODULE JAVASCRIPT KHÁC Ở ĐÂY
    // ==========================================================
    
});