@if (session('success'))
    <div id="success-notification" class="notification-container">
        <div class="notification success-notification animate__animated animate__slideInRight">
            <div class="notification-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Thành công!</div>
                <div class="notification-message">{!! nl2br(e(session('success'))) !!}</div>
            </div>
            <button class="notification-close" onclick="closeNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <style>
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .notification {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 10px;
            border-left: 5px solid #4CAF50;
        }

        .notification-icon {
            font-size: 24px;
            color: #4CAF50;
            flex-shrink: 0;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #fff;
        }

        .notification-message {
            font-size: 14px;
            line-height: 1.4;
            color: #f0f0f0;
            white-space: pre-line;
        }

        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .notification-close:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .success-notification {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }

        .error-notification {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            border-left-color: #f44336;
        }

        .warning-notification {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            border-left-color: #ff9800;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .animate__slideInRight {
            animation: slideInRight 0.5s ease-out;
        }

        .animate__slideOutRight {
            animation: slideOutRight 0.5s ease-in;
        }

        @media (max-width: 768px) {
            .notification-container {
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
    </style>

    <script>
        function closeNotification() {
            const notification = document.getElementById('success-notification');
            if (notification) {
                notification.querySelector('.notification').classList.remove('animate__slideInRight');
                notification.querySelector('.notification').classList.add('animate__slideOutRight');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }

        // Tự động đóng sau 8 giây
        setTimeout(() => {
            closeNotification();
        }, 8000);
    </script>
@endif

@if (session('error'))
    <div id="error-notification" class="notification-container">
        <div class="notification error-notification animate__animated animate__slideInRight">
            <div class="notification-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Lỗi!</div>
                <div class="notification-message">{!! nl2br(e(session('error'))) !!}</div>
            </div>
            <button class="notification-close" onclick="closeErrorNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        function closeErrorNotification() {
            const notification = document.getElementById('error-notification');
            if (notification) {
                notification.querySelector('.notification').classList.remove('animate__slideInRight');
                notification.querySelector('.notification').classList.add('animate__slideOutRight');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }

        // Tự động đóng sau 8 giây
        setTimeout(() => {
            closeErrorNotification();
        }, 8000);
    </script>
@endif 