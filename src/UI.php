<?php
class UI {
    public static function toast(string $message, string $type = 'success', int $duration = 3000): string {
        $icons = [
            'success' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            'error' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            'warning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            'info' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ];
        
        $colors = [
            'success' => 'bg-green-500',
            'error' => 'bg-red-500',
            'warning' => 'bg-yellow-500',
            'info' => 'bg-blue-500'
        ];
        
        return <<<HTML
        <div id="toast-{$type}" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl {$colors[$type]} text-white shadow-lg transform transition-all duration-300 translate-y-[-100px] opacity-0">
            <span class="flex-shrink-0">{$icons[$type]}</span>
            <span class="font-semibold text-sm">{$message}</span>
        </div>
        <script>
            (function() {
                const toast = document.getElementById('toast-{$type}');
                setTimeout(() => {
                    toast.classList.remove('translate-y-[-100px]', 'opacity-0');
                }, 10);
                setTimeout(() => {
                    toast.classList.add('translate-y-[-100px]', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, {$duration});
            })();
        </script>
        HTML;
    }

    public static function loadingSpinner(string $size = 'md'): string {
        $sizes = [
            'sm' => 'w-5 h-5',
            'md' => 'w-8 h-8',
            'lg' => 'w-12 h-12'
        ];
        return <<<HTML
        <div class="flex items-center justify-center">
            <div class="{$sizes[$size]} animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        </div>
        HTML;
    }

    public static function loadingOverlay(string $id = 'loading-overlay'): string {
        return <<<HTML
        <div id="{$id}" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[90] hidden items-center justify-center">
            <div class="bg-white rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-3">
                <div class="w-10 h-10 animate-spin rounded-full border-4 border-gray-100 border-t-brand-500"></div>
                <span class="text-sm font-semibold text-gray-600">Carregando...</span>
            </div>
        </div>
        HTML;
    }

    public static function emptyState(string $title, string $description, string $buttonText = '', string $buttonAction = ''): string {
        $button = $buttonText ? 
            "<button onclick=\"{$buttonAction}\" class=\"mt-4 bg-brand-50 text-brand-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-brand-100 transition-colors\">{$buttonText}</button>" : '';
        
        return <<<HTML
        <div class="text-center py-16 bg-white rounded-[2rem] border border-white shadow-premium">
            <div class="text-6xl mb-4">🐱</div>
            <p class="text-gray-800 font-display font-bold text-lg">{$title}</p>
            <p class="text-gray-400 text-sm mt-2">{$description}</p>
            {$button}
        </div>
        HTML;
    }

    public static function skeletonCard(): string {
        return <<<HTML
        <div class="animate-pulse flex space-x-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="rounded-full bg-slate-200 h-16 w-16"></div>
            <div class="flex-1 space-y-3 py-1">
                <div class="h-2 bg-slate-200 rounded"></div>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="h-2 bg-slate-200 rounded col-span-2"></div>
                        <div class="h-2 bg-slate-200 rounded col-span-1"></div>
                    </div>
                    <div class="h-2 bg-slate-200 rounded"></div>
                </div>
            </div>
        </div>
        HTML;
    }

    public static function confirmModal(string $id, string $title, string $message, string $confirmText = 'Confirmar', string $confirmAction = '', string $type = 'danger'): string {
        $colors = [
            'danger' => 'bg-red-500 hover:bg-red-600',
            'warning' => 'bg-yellow-500 hover:bg-yellow-600',
            'info' => 'bg-blue-500 hover:bg-blue-600'
        ];
        
        $icon = $type === 'danger' ? 
            '<svg class="w-12 h-12 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' :
            '<svg class="w-12 h-12 mx-auto text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        return <<<HTML
        <div id="{$id}" class="fixed inset-0 z-[80] bg-black/50 hidden items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
                <div class="p-6 text-center">
                    {$icon}
                    <h3 class="text-xl font-display font-extrabold text-gray-800 mb-2">{$title}</h3>
                    <p class="text-gray-500 text-sm mb-6">{$message}</p>
                    <div class="flex gap-3">
                        <button onclick="closeConfirmModal('{$id}')" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors">Cancelar</button>
                        <button onclick="{$confirmAction}; closeConfirmModal('{$id}')" class="flex-1 {$colors[$type]} text-white font-bold py-3 rounded-xl transition-colors">{$confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function openConfirmModal_{$id}() {
                document.getElementById('{$id}').classList.remove('hidden');
                document.getElementById('{$id}').classList.add('flex');
            }
            function closeConfirmModal(id) {
                document.getElementById(id).classList.add('hidden');
                document.getElementById(id).classList.remove('flex');
            }
        </script>
        HTML;
    }

    public static function pagination(int $currentPage, int $totalPages, string $baseUrl, array $params = []): string {
        if ($totalPages <= 1) return '';
        
        $prevDisabled = $currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : '';
        $nextDisabled = $currentPage >= $totalPages ? 'opacity-50 cursor-not-allowed' : '';
        
        $url = function($page) use ($baseUrl, $params) {
            $params['page'] = $page;
            return $baseUrl . '?' . http_build_query($params);
        };
        
        $pages = [];
        for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
            $active = $i === $currentPage ? 'bg-brand-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50';
            $pages[] = "<a href=\"{$url($i)}\" class=\"w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm {$active}\">{$i}</a>";
        }
        
        return <<<HTML
        <div class="flex justify-center items-center gap-2 mt-6">
            <a href="{$url($currentPage - 1)}" class="w-10 h-10 rounded-lg flex items-center justify-center bg-white text-gray-600 hover:bg-gray-50 {$prevDisabled}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="flex gap-1">
                ${implode('', $pages)}
            </div>
            <a href="{$url($currentPage + 1)}" class="w-10 h-10 rounded-lg flex items-center justify-center bg-white text-gray-600 hover:bg-gray-50 {$nextDisabled}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        HTML;
    }
}
