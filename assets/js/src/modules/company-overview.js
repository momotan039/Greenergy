/**
 * Company Overview Block Interactivity
 */

export function initCompanyOverview() {
    const cards = document.querySelectorAll('.company-overview-card');

    cards.forEach(card => {
        const toggle = card.querySelector('.js-contact-toggle');
        const info = card.querySelector('.js-contact-info');
        const icon = toggle?.querySelector('i');

        if (!toggle || !info) return;

        // Avoid duplicate event listeners
        if (toggle.dataset.initialized) return;
        toggle.dataset.initialized = 'true';

        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            
            const isOpen = info.classList.contains('grid-rows-[1fr]');

            if (isOpen) {
                // Close
                info.classList.replace('grid-rows-[1fr]', 'grid-rows-[0fr]');
                if (icon) icon.style.transform = 'rotate(0deg)';
                // Optional: remove margin if needed, but our structure handled it
            } else {
                // Open
                info.classList.replace('grid-rows-[0fr]', 'grid-rows-[1fr]');
                if (icon) icon.style.transform = 'rotate(180deg)';
            }
        });
    });
}
