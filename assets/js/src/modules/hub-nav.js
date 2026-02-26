/**
 * Hub Navigation Module
 * 
 * Handles smooth transition between Courses and Articles via AJAX.
 */

export function initHubNav() {
    const hubNav = document.querySelector('.js-hub-nav');
    if (!hubNav) return;

    document.addEventListener('click', function(e) {
        const link = e.target.closest('.js-hub-nav-link');
        if (!link) return;

        // Only handle if it's a hub link and we are on a page that has a hub content area
        const hubArea = document.querySelector('.js-hub-content-view') || document.querySelector('#primary');
        if (!hubArea) return;

        e.preventDefault();
        const url = link.href;
        const type = link.dataset.type;

        // 1. Show Loading State
        document.body.classList.add('is-hub-navigating');
        hubArea.classList.add('transition-all', 'duration-500', 'opacity-0', 'scale-95');
        hubArea.style.pointerEvents = 'none';

        // 2. Fetch the target page
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newContent = doc.querySelector('.js-hub-content-view') || doc.querySelector('#primary');
                
                if (newContent) {
                    setTimeout(() => {
                        document.title = doc.title;
                        history.pushState({ type: type }, doc.title, url);
                        hubArea.innerHTML = newContent.innerHTML;
                        
                        if (newContent.id === 'primary' && hubArea.id === 'primary') {
                            hubArea.className = newContent.className;
                        }

                        reinitModules();
                        
                        // Fade In
                        hubArea.classList.remove('opacity-0', 'scale-95');
                        hubArea.classList.add('opacity-100', 'scale-100');
                    }, 300); // Small delay for smooth feel
                } else {
                    window.location.href = url;
                }
            })
            .catch(err => {
                console.error('Hub Nav Error:', err);
                window.location.href = url;
            })
            .finally(() => {
                setTimeout(() => {
                    hubArea.style.pointerEvents = 'all';
                    document.body.classList.remove('is-hub-navigating');
                }, 500);
            });
    });

    // Handle Back/Forward buttons
    window.addEventListener('popstate', function() {
        window.location.reload(); // Simple approach for now
    });
}

function reinitModules() {
    // Re-init AOS
    if (typeof AOS !== 'undefined') {
        AOS.refreshHard();
        AOS.init();
    }

    // Since we are using bundled modules, we might need a global registry
    // or just re-dispatch a custom event that main.js listens to.
    document.dispatchEvent(new CustomEvent('greenergy:content-updated'));
}
