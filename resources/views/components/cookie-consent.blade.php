<div id="cookie-consent-banner" class="hidden fixed bottom-0 left-0 right-0 z-[10000] bg-white border-t-2 border-indigo-600 shadow-2xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">We use cookies</h3>
                <p class="text-sm text-gray-600">
                    We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. 
                    By clicking "Accept All", you consent to our use of cookies. 
                    <a href="/policies#cookies-section" class="text-indigo-600 hover:underline">Learn more</a> about our cookie policy.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    id="cookie-consent-accept" 
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition whitespace-nowrap"
                >
                    Accept All
                </button>
                <button 
                    id="cookie-consent-decline" 
                    class="bg-white text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition border-2 border-gray-300 whitespace-nowrap"
                >
                    Decline
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('cookie-consent-banner');
    const acceptBtn = document.getElementById('cookie-consent-accept');
    const declineBtn = document.getElementById('cookie-consent-decline');
    
    // Check if consent has been given
    const consent = localStorage.getItem('cookie-consent');
    
    if (!consent) {
        // Show banner after a short delay
        setTimeout(() => {
            banner.classList.remove('hidden');
            banner.classList.add('animate-slide-up');
        }, 1000);
    }
    
    acceptBtn.addEventListener('click', function() {
        localStorage.setItem('cookie-consent', 'accepted');
        localStorage.setItem('cookie-consent-date', new Date().toISOString());
        hideBanner();
    });
    
    declineBtn.addEventListener('click', function() {
        localStorage.setItem('cookie-consent', 'declined');
        localStorage.setItem('cookie-consent-date', new Date().toISOString());
        hideBanner();
    });
    
    function hideBanner() {
        banner.classList.add('animate-slide-down');
        setTimeout(() => {
            banner.classList.add('hidden');
        }, 300);
    }
});
</script>

<style>
@keyframes slide-up {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slide-down {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100%);
        opacity: 0;
    }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out forwards;
}

.animate-slide-down {
    animation: slide-down 0.3s ease-out forwards;
}
</style>

