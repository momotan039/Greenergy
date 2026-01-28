/**
 * Counter Animation Module
 */

export const initCounter = () => {
    const counters = document.querySelectorAll('.js-counter');

    if (counters.length === 0) return;

    const countUp = (el) => {
        const target = parseFloat(el.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const stepTime = 20; // 20ms intervals
        const steps = duration / stepTime;
        const increment = target / steps;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                el.innerText = Math.floor(target).toLocaleString();
                clearInterval(timer);
            } else {
                el.innerText = Math.floor(current).toLocaleString();
            }
        }, stepTime);
    };

    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                countUp(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => {
        observer.observe(counter);
    });
};
