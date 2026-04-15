<?php
// src/alerts.php
// Helper para exibição de alertas (Toast/Flash messages)

function set_flash_message($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'message' => $message
    ];
}

function display_flash_message()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        $bg_color = 'bg-blue-500';
        $icon = 'ℹ️';

        if ($flash['type'] == 'success') {
            $bg_color = 'bg-green-500';
            $icon = '✅';
        }
        else if ($flash['type'] == 'error') {
            $bg_color = 'bg-red-500';
            $icon = '❌';
        }
        else if ($flash['type'] == 'warning') {
            $bg_color = 'bg-yellow-500';
            $icon = '⚠️';
        }

        echo "
        <div id='flash-message' class='fixed top-4 right-4 z-50 flex items-center p-4 mb-4 text-white rounded-lg shadow-lg transition-opacity duration-300 $bg_color' role='alert'>
            <span class='text-xl mr-2'>$icon</span>
            <div class='text-sm font-medium'>{$flash['message']}</div>
            <button type='button' onclick='document.getElementById(\"flash-message\").remove()' class='ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-black/20 inline-flex h-8 w-8 items-center justify-center' aria-label='Close'>
                <span class='sr-only'>Close</span>
                <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' d='M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z' clip-rule='evenodd'></path></svg>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const flash = document.getElementById('flash-message');
                if (flash) {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 300);
                }
            }, 3000);
        </script>
        ";
    }
}
?>
