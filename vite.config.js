// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Các file CSS và JS đầu vào
            input: [
                // CSS của Velzon
                'resources/assets_velzon/css/bootstrap.min.css',
                'resources/assets_velzon/css/icons.min.css',
                'resources/assets_velzon/css/app.min.css',
                'resources/assets_velzon/css/custom.min.css',
                
                // JS của Velzon (và các thư viện nó cần)
                'resources/assets_velzon/libs/bootstrap/js/bootstrap.bundle.min.js',
                'resources/assets_velzon/libs/simplebar/simplebar.min.js',
                'resources/assets_velzon/libs/node-waves/waves.min.js',
                'resources/assets_velzon/libs/feather-icons/feather.min.js',
                'resources/assets_velzon/js/pages/plugins/lord-icon-2.1.0.js',
                'resources/assets_velzon/js/plugins.js',
                'resources/assets_velzon/js/app.js', // File JS chính của Velzon

                // File JS tùy chỉnh của bạn
                'resources/js/app.js', 
            ],
            refresh: true,
        }),
    ],
});