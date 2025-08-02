// File: resources/js/admin-chat.js

// Dòng này rất quan trọng, nó nạp cấu hình Echo từ bootstrap.js
import './bootstrap';

document.addEventListener("DOMContentLoaded", function () {
    console.log("Admin Chat Script Loaded!");

    // --- Lấy tất cả các Element cần thiết ---
    const searchInput = document.getElementById('user-search-input');
    const searchBtn = document.getElementById('user-search-btn');
    const userListContainer = document.getElementById('user-list-container');
    const chatWindow = document.getElementById('chat-window');
    const noChatSelected = document.getElementById('no-chat-selected');
    const messagesContainer = document.getElementById('chat-messages-container');
    const chatHeader = document.getElementById('chat-header-main');
    const chatInput = document.getElementById('chat-message-input');
    const sendBtn = document.getElementById('send-message-btn');
    const adminIdMeta = document.querySelector('meta[name="user-id"]'); // Lấy thẻ meta

    // Kiểm tra xem các element chính có tồn tại không
    if (!searchInput || !searchBtn || !userListContainer || !chatWindow || !adminIdMeta) {
        console.error("Lỗi: Thiếu một trong các element cần thiết cho chat admin hoặc meta user-id.");
        return;
    }

    // --- Các biến trạng thái ---
    let currentUserId = null;
    let currentUserName = '';
    const adminId = parseInt(adminIdMeta.getAttribute('content')); // Lấy ID admin từ thẻ meta
    const initialUsersHtml = userListContainer.innerHTML;

    // --- Các hàm chức năng ---
    const renderMessage = (message) => {
        const item = document.createElement('div');
        item.className = 'message-item ' + (message.sender_id === adminId ? 'sent' : 'received');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-content';
        messageDiv.innerText = message.message;
        item.appendChild(messageDiv);
        messagesContainer.appendChild(item);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const displayUsers = (users) => {
        userListContainer.innerHTML = '';
        if (users.length === 0) {
            userListContainer.innerHTML = '<p class="text-center text-muted p-3">Không tìm thấy khách hàng nào.</p>';
            return;
        }
        users.forEach(user => {
            const userItem = document.createElement('div');
            userItem.className = 'user-item';
            userItem.dataset.userId = user.id;
            userItem.dataset.userName = user.name;
            userItem.innerHTML = `<div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div><span class="user-name">${user.name}</span>`;
            userListContainer.appendChild(userItem);
        });
    };

    const performSearch = async () => {
        const query = searchInput.value.trim();
        if (query === '') {
            userListContainer.innerHTML = initialUsersHtml;
            return;
        }
        if (query.length < 2) return;
        userListContainer.innerHTML = '<div class="text-center p-4"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
        try {
            const response = await fetch(`/admin/chat/search-users?q=${query}`);
            if (!response.ok) throw new Error(`Lỗi server: ${response.status}`);
            const users = await response.json();
            displayUsers(users);
        } catch (error) {
            console.error("Lỗi khi tìm kiếm:", error);
            userListContainer.innerHTML = '<p class="text-center text-danger p-3">Đã xảy ra lỗi khi tìm kiếm.</p>';
        }
    };
    
    const sendMessage = () => {
        if (!currentUserId || chatInput.value.trim() === '') return;
        const messageText = chatInput.value;
        chatInput.value = '';
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: messageText, receiver_id: currentUserId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                renderMessage(data.message);
            }
        })
        .catch(error => console.error('Lỗi khi gửi tin nhắn:', error));
    };

    // --- Gán các sự kiện ---
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', (e) => (e.key === 'Enter' && performSearch()));
    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => (e.key === 'Enter' && sendMessage()));

    userListContainer.addEventListener('click', async function (event) {
        const userItem = event.target.closest('.user-item');
        if (!userItem) return;
        
        document.querySelectorAll('.user-item.active').forEach(el => el.classList.remove('active'));
        userItem.classList.add('active');
        currentUserId = parseInt(userItem.dataset.userId);
        currentUserName = userItem.dataset.userName;

        noChatSelected.classList.add('d-none');
        chatWindow.classList.remove('d-none');
        chatHeader.innerText = `Trò chuyện với ${currentUserName}`;
        messagesContainer.innerHTML = '<p class="text-center text-muted">Đang tải lịch sử...</p>';

        try {
            const response = await fetch(`/chat/history/${currentUserId}`);
            const messages = await response.json();
            messagesContainer.innerHTML = '';
            messages.forEach(msg => renderMessage(msg));

            const participants = [adminId, currentUserId].sort((a, b) => a - b);
            const channelName = `chat.${participants[0]}.${participants[1]}`;
            
            if (window.Echo) {
                if (window.Echo.privateChannel) window.Echo.leave(window.Echo.privateChannel);
                window.Echo.privateChannel = channelName;
                
                window.Echo.private(channelName)
                    .listen('.new-message', (e) => {
                        if (e.message.sender_id === currentUserId) {
                            renderMessage(e.message);
                        }
                    });
            }
        } catch (error) {
            console.error('Lỗi khi mở cuộc trò chuyện:', error);
            messagesContainer.innerHTML = '<p class="text-center text-danger">Không thể tải cuộc trò chuyện.</p>';
        }
    });
});