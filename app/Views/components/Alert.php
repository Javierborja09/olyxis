<?php if (isset($message) && $message): ?>
    <div id="alert-container" style="padding: 10px; background: <?= $type === 'success' ? '#dff0d8' : '#f2dede' ?>; color: <?= $type === 'success' ? '#3c763d' : '#a94442' ?>; margin-bottom: 15px; border-radius: 4px; transition: opacity 0.5s ease;">
        <?= htmlspecialchars($message) ?>
    </div>

    <script>
        setTimeout(function() {
            const alert = document.getElementById('alert-container');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 2000);
    </script>
<?php endif; ?>