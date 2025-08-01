@auth
@push('styles')
<style>
    /* CSS cho Chat Widget */
    #chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 320px;
        max-width: 90%;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        z-index: 1000;
        transition: transform 0.3s ease-in-out, height 0.3s ease-in-out;
        overflow: hidden; /* Ngăn nội dung tràn ra ngoài khi thu nhỏ */
    }

    /* === BẮT ĐẦU PHẦN SỬA LỖI === */
    /* Trạng thái thu nhỏ: Ẩn body và footer, chỉ hiện header */
    #chat-widget.collapsed {
        height: 50px; /* Chiều cao chỉ bằng header */
    }
    #chat-widget.collapsed .chat-body,
    #chat-widget.collapsed .chat-footer {
        display: none;
    }
    #toggle-chat-btn {
        transition: transform 0.3s;
    }
    #chat-widget.collapsed #toggle-chat-btn {
        transform: rotate(180deg); /* Xoay icon nút bấm */
    }
    /* === KẾT THÚC PHẦN SỬA LỖI === */

    .chat-header {
        height: 50px; /* Đặt chiều cao cố định cho header */
        background-color: #ea73ac;
        color: white;
        padding: 12px 15px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .chat-header i { margin-right: 10px; }
    #toggle-chat-btn {
        margin-left: auto; background: none; border: none;
        color: white; font-size: 20px; cursor: pointer;
    }
    .chat-body {
        height: 300px; padding: 10px; overflow-y: auto;
    }
    #chat-messages {
        list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column;
    }
    #chat-messages li {
        margin-bottom: 10px; padding: 8px 12px; border-radius: 15px; max-width: 80%;
        word-wrap: break-word; background-color: #f1f0f0; align-self: flex-start;
    }
    #chat-messages li.my-message {
        background-color: #ea73ac; color: white; align-self: flex-end;
    }
    .chat-footer {
        display: flex; align-items: center; padding: 10px; background-color: #f9f9f9;
    }
    #chat-input {
        flex-grow: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; outline: none;
    }
    #send-chat-btn {
        background-color: #ea73ac; color: white; border: none;
        border-radius: 50%; width: 40px; height: 40px; margin-left: 10px; cursor: pointer;
    }
</style>
@endpush

<div id="chat-widget" class="collapsed">
    <div id="chat-header" class="chat-header">
        <i class="fas fa-comment-dots"></i>
        <span>Hỗ trợ trực tuyến</span>
        {{-- Thay đổi icon ban đầu --}}
        <button id="toggle-chat-btn">^</button>
    </div>
    <div id="chat-body" class="chat-body">
        <ul id="chat-messages">
            <li>Chào mừng bạn! Chúng tôi có thể giúp gì cho bạn?</li>
        </ul>
    </div>
    <div id="chat-footer" class="chat-footer">
        <input type="text" id="chat-input" placeholder="Nhập tin nhắn...">
        <button id="send-chat-btn"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
@endauth