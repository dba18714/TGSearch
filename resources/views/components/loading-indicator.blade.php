<style>
    @keyframes pulse {
        0%, 100% {
            transform: scale(0.5);
            opacity: 0.2;
        }
        50% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .loader-dot {
        animation: pulse 1.4s ease-in-out infinite;
    }

    .loader-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .loader-dot:nth-child(3) {
        animation-delay: 0.4s;
    }
</style>

<div wire:loading.delay 
     class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 
            bg-gray-100/95 dark:bg-gray-900/95 
            backdrop-blur-md
            rounded-xl 
            shadow-[0_0_15px_rgba(0,0,0,0.1)] dark:shadow-[0_0_15px_rgba(0,0,0,0.5)]
            border border-gray-200 dark:border-gray-700
            px-6 py-5 
            whitespace-nowrap">
    <div class="flex space-x-3">
        <div class="loader-dot w-5 h-5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
        <div class="loader-dot w-5 h-5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
        <div class="loader-dot w-5 h-5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
    </div>
</div>