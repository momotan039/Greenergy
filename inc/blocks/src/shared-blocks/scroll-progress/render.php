<?php
/**
 * Scroll Progress Block Template.
 *
 * @package Greenergy
 */
?>

<!-- Scroll Progress Indicator -->
<div id="scroll-progress-indicator" class="max-md:hidden fixed top-10 right-10 z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
    <div class="w-20 h-20 p-2.5 relative bg-sky-500 rounded-[100px] shadow-[0px_4px_14px_0px_rgba(38,159,192,1.00)] inline-flex justify-center items-center gap-2.5 overflow-hidden">
        <!-- Fill (Moves from top down based on scroll) -->
        <div id="scroll-progress-fill" class="w-20 h-20 left-0 top-[70px] absolute origin-top-left -rotate-90 bg-gradient-to-b from-sky-500 to-blue-700 rounded-full transition-all duration-100 ease-out"></div>
        
        <!-- Text -->
        <div id="scroll-progress-text" class="relative z-10 text-center justify-start text-white text-base font-bold font-['DIN_Next_LT_Arabic'] leading-[48px]">0%</div>
    </div>
</div>


<style>
    @keyframes particle-scatter {
        0% {
            transform: translate(0, 0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(var(--tx), var(--ty)) scale(0);
            opacity: 0;
        }
    }
    .particle {
        position: fixed;
        width: 8px;
        height: 8px;
        background: #0ea5e9; /* sky-500 */
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        animation: particle-scatter 0.8s ease-out forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const indicator = document.getElementById('scroll-progress-indicator');
        const fill = document.getElementById('scroll-progress-fill');
        const text = document.getElementById('scroll-progress-text');
        
        let requestId;

        // Ensure elements exist
        if (!indicator || !fill || !text) return;

        function updateScroll() {
            // Calculate scroll percentage
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            
            // Avoid division by zero
            if (scrollHeight <= 0) return;

            const scrollPercent = Math.min(Math.max(scrollTop / scrollHeight, 0), 1);
            const percentage = Math.round(scrollPercent * 100);

            // Update UI
            text.textContent = percentage + '%';
            
            // Map percentage to position. 
            const offset = 70 - (70 * scrollPercent);
            fill.style.top = `${offset}px`;

            // Toggle visibility logic
            if (scrollTop > 100) {
                indicator.classList.remove('opacity-0', 'pointer-events-none');
                indicator.classList.add('cursor-pointer', 'pointer-events-auto');
            } else {
                indicator.classList.add('opacity-0', 'pointer-events-none');
                indicator.classList.remove('cursor-pointer', 'pointer-events-auto');
            }
            
            requestId = null;
        }

        // Particle effect function
        function createParticles(x, y) {
            const particleCount = 12;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random destination using CSS custom properties
                // Try to scatter in all directions but with some downward tendency for gravity
                const angle = Math.random() * Math.PI * 2;
                const distance = 50 + Math.random() * 50;
                const tx = Math.cos(angle) * distance;
                const ty = Math.sin(angle) * distance + 30; // +30 for gravity

                particle.style.setProperty('--tx', `${tx}px`);
                particle.style.setProperty('--ty', `${ty}px`);
                
                // Random color variations (sky-500 to blue-700)
                const colors = ['#0ea5e9', '#0284c7', '#0369a1', '#22c55e'];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                
                particle.style.left = `${x}px`;
                particle.style.top = `${y}px`;
                
                document.body.appendChild(particle);
                
                // Cleanup
                particle.addEventListener('animationend', () => {
                    particle.remove();
                });
            }
        }

        // Click to scroll to top + particles
        indicator.addEventListener('click', function(e) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // Get center position of the button
            const rect = indicator.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            createParticles(centerX, centerY);
        });

        // Optimize with rAF
        window.addEventListener('scroll', function() {
            if (!requestId) {
                requestId = window.requestAnimationFrame(updateScroll);
            }
        });

        // Initial call
        updateScroll();
    });
</script>
