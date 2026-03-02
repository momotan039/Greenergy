/**
 * Product detail modal (company products block)
 * Opens modal with variant selector when clicking "عرض التفاصيل".
 * If only one variant → open download popup directly. X on download returns to variant list.
 */
export function initProductModal() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.greenergy-product-detail-btn[data-modal-content-id]');
        if (btn) {
            e.preventDefault();
            const modalId = btn.getAttribute('data-modal-id') + '-modal';
            const contentId = btn.getAttribute('data-modal-content-id');
            const modal = document.getElementById(modalId);
            const contentSlot = modal && modal.querySelector('.greenergy-product-modal-content');
            const src = document.getElementById(contentId);
            if (!modal || !contentSlot || !src) return;
            contentSlot.innerHTML = src.innerHTML;
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            const section = contentSlot.querySelector('.greenergy-select-variant-product');
            const blocks = section && section.querySelectorAll('.greenergy-variant-download-block');
            const downloadModalId = modalId.replace(/-modal$/, '-download-modal');
            const downloadModal = document.getElementById(downloadModalId);
            const downloadContentSlot = downloadModal && downloadModal.querySelector('.greenergy-download-modal-content');

            if (blocks && blocks.length === 1 && downloadModal && downloadContentSlot) {
                downloadContentSlot.innerHTML = blocks[0].innerHTML;
                downloadModal.setAttribute('data-single-variant', 'true');
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                downloadModal.style.display = 'flex';
                downloadModal.setAttribute('aria-hidden', 'false');
            }
            return;
        }

        const closeBtn = e.target.closest('.greenergy-product-modal-close');
        const backdrop = e.target.closest('.greenergy-product-modal-backdrop');
        if (closeBtn || backdrop) {
            const modal = e.target.closest('.greenergy-product-modal');
            if (modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                const slot = modal.querySelector('.greenergy-product-modal-content');
                if (slot) slot.innerHTML = '';
                document.body.style.overflow = '';
            }
            return;
        }

        const variantOption = e.target.closest('.js-select-variant');
        if (variantOption) {
            e.preventDefault();
            const idx = variantOption.getAttribute('data-variant-index');
            if (idx == null) return;
            const section = variantOption.closest('.greenergy-select-variant-product');
            if (!section) return;
            const firstModal = section.closest('.greenergy-product-modal');
            if (!firstModal || !firstModal.id) return;
            const block = section.querySelector('.greenergy-variant-download-block[data-variant-index="' + idx + '"]');
            const downloadModalId = firstModal.id.replace(/-modal$/, '-download-modal');
            const downloadModal = document.getElementById(downloadModalId);
            const downloadContentSlot = downloadModal && downloadModal.querySelector('.greenergy-download-modal-content');
            if (block && downloadModal && downloadContentSlot) {
                downloadModal.removeAttribute('data-single-variant');
                downloadContentSlot.innerHTML = block.innerHTML;
                firstModal.style.display = 'none';
                firstModal.setAttribute('aria-hidden', 'true');
                downloadModal.style.display = 'flex';
                downloadModal.setAttribute('aria-hidden', 'false');
            }
            section.querySelectorAll('.greenergy-variant-option').forEach((el) => {
                el.classList.remove('bg-green-100', 'ring-2', 'ring-green-600', 'ring-offset-2');
                if (el.getAttribute('data-variant-index') === idx) {
                    el.classList.add('bg-green-100', 'ring-2', 'ring-green-600', 'ring-offset-2');
                }
            });
            return;
        }

        const downloadModalClose = e.target.closest('.greenergy-download-modal-close');
        const downloadModalBackdrop = e.target.closest('.greenergy-download-modal-backdrop');
        if (downloadModalClose || downloadModalBackdrop) {
            const downloadModal = e.target.closest('.greenergy-product-download-modal');
            if (!downloadModal || !downloadModal.id) return;
            const firstModalId = downloadModal.id.replace(/-download-modal$/, '-modal');
            const firstModal = document.getElementById(firstModalId);
            const isSingleVariant = downloadModal.getAttribute('data-single-variant') === 'true';
            downloadModal.style.display = 'none';
            downloadModal.setAttribute('aria-hidden', 'true');
            downloadModal.removeAttribute('data-single-variant');
            const slot = downloadModal.querySelector('.greenergy-download-modal-content');
            if (slot) slot.innerHTML = '';
            if (isSingleVariant) {
                if (firstModal) {
                    firstModal.style.display = 'none';
                    firstModal.setAttribute('aria-hidden', 'true');
                    const firstSlot = firstModal.querySelector('.greenergy-product-modal-content');
                    if (firstSlot) firstSlot.innerHTML = '';
                }
                document.body.style.overflow = '';
            } else if (firstModal) {
                firstModal.style.display = 'flex';
                firstModal.setAttribute('aria-hidden', 'false');
            }
            return;
        }

        const shareWa = e.target.closest('.greenergy-share-whatsapp');
        if (shareWa) {
            e.preventDefault();
            const fileUrl = shareWa.getAttribute('data-file-url') || '';
            const wrap = shareWa.closest('.greenergy-download-product-file');
            const numInput = wrap && wrap.querySelector('.greenergy-whatsapp-number');
            const num = (numInput && numInput.value.trim()) ? numInput.value.trim().replace(/\D/g, '') : '';
            const text = fileUrl ? encodeURIComponent('رابط التحميل: ' + fileUrl) : '';
            const url = num ? `https://wa.me/${num}${text ? '?text=' + text : ''}` : 'https://wa.me/';
            window.open(url, '_blank', 'noopener');
            return;
        }
        const shareEmail = e.target.closest('.greenergy-share-email');
        if (shareEmail) {
            e.preventDefault();
            const fileUrl = shareEmail.getAttribute('data-file-url') || '';
            const wrap = shareEmail.closest('.greenergy-download-product-file');
            const emailInput = wrap && wrap.querySelector('.greenergy-email-input');
            const to = (emailInput && emailInput.value.trim()) ? emailInput.value.trim() : '';
            const subject = encodeURIComponent('رابط تحميل الملف');
            const body = encodeURIComponent(fileUrl ? 'رابط التحميل: ' + fileUrl : '');
            const mailto = `mailto:${to || ''}?subject=${subject}&body=${body}`;
            window.location.href = mailto;
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const variantOption = e.target.closest('.js-select-variant');
        if (!variantOption) return;
        e.preventDefault();
        variantOption.click();
    });
}
