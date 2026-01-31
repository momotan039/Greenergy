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
            // At 0%, top should be 56px (hidden at bottom). 
            // At 100%, top should be 0px (fully filling).
            // Actually, based on the description "top-[56px]", it seems it starts offset. 
            // Let's assume the gradient moves UP into view.
            // Wait, "origin-top-left -rotate-90" suggests rotation.
            // Let's stick to the visual manipulation: changing 'top' from 56px to 0px.
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

        // Click to scroll to top
        indicator.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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
