<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel PWA Demo</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('public/manifest.json') }}">
    <meta name="theme-color" content="#0d6efd">

    <!-- Example CSS -->
  
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #f9f9f9;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
        }
        .online { color: green; }
        .offline { color: red; }
    </style>

    <!-- Register Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/public/service-worker.js')
                    .then(reg => console.log('ServiceWorker registered:', reg.scope))
                    .catch(err => console.log('ServiceWorker registration failed:', err));
            });
        }
    </script>
</head>
<body>

    <div class="card">
        <h1>Laravel PWA Demo</h1>
        <p>Status: <span id="status" class="online">Online</span></p>

        <a href="{{ route('pos.pos-bill') }}" class="btn btn-primary">
            Go to POS Bill
        </a>

        <p style="margin-top: 20px;">
            Try going offline and refreshing this page.  
            You should still see it because of caching!
        </p>
    </div>

    <script>
        // Show online/offline status
        function updateStatus() {
            const statusEl = document.getElementById('status');
            if (navigator.onLine) {
                statusEl.textContent = "Online";
                statusEl.className = "online";
            } else {
                statusEl.textContent = "Offline";
                statusEl.className = "offline";
            }
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        updateStatus();
    </script>

</body>
</html>
