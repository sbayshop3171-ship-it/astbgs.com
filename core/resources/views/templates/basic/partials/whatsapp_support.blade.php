<style>
    .whatsapp-support {
        position: fixed !important;
        right: 30px !important;
        bottom: 94px !important;
        width: 56px !important;
        height: 56px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        background: #25d366 !important;
        color: #fff !important;
        text-decoration: none !important;
        box-shadow: 0 14px 28px rgba(37, 211, 102, 0.28) !important;
        z-index: 9999 !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease !important;
    }

    .whatsapp-support:hover,
    .whatsapp-support:focus-visible {
        color: #fff !important;
        background: #20bd5a !important;
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(37, 211, 102, 0.34) !important;
        outline: none !important;
    }

    .whatsapp-support__icon {
        width: 28px;
        height: 28px;
        display: block;
        fill: currentColor;
    }

    .whatsapp-support__text {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    @media (max-width: 575px) {
        .whatsapp-support {
            right: 18px !important;
            bottom: 150px !important;
            width: 52px !important;
            height: 52px !important;
        }

        .whatsapp-support__icon {
            width: 26px;
            height: 26px;
        }
    }
</style>

<a href="https://wa.me/18456221634"
    class="whatsapp-support"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="@lang('Chat on WhatsApp')"
    title="@lang('Chat on WhatsApp')">
    <svg class="whatsapp-support__icon" viewBox="0 0 32 32" aria-hidden="true">
        <path
            d="M19.11 17.34c-.29-.15-1.72-.85-1.98-.94-.27-.1-.47-.15-.67.15-.19.29-.76.94-.93 1.13-.17.19-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.08-.15-.67-1.62-.92-2.22-.24-.58-.48-.5-.67-.51h-.57c-.19 0-.51.07-.77.36-.27.29-1.01.99-1.01 2.42 0 1.42 1.03 2.8 1.18 3 .15.19 2.03 3.11 4.92 4.36.69.3 1.23.48 1.65.61.69.22 1.33.19 1.83.12.56-.08 1.72-.7 1.96-1.37.24-.67.24-1.24.17-1.37-.06-.12-.24-.19-.53-.34Z" />
        <path
            d="M16.02 3.2c-7 0-12.69 5.67-12.69 12.65 0 2.23.59 4.42 1.71 6.35L3.2 28.8l6.78-1.78a12.72 12.72 0 0 0 6.04 1.53h.01c7 0 12.69-5.68 12.69-12.66 0-3.38-1.32-6.55-3.72-8.94A12.63 12.63 0 0 0 16.02 3.2Zm0 23.16h-.01a10.56 10.56 0 0 1-5.38-1.47l-.39-.23-4.02 1.05 1.07-3.92-.25-.4a10.44 10.44 0 0 1-1.61-5.53c0-5.77 4.72-10.47 10.53-10.47 2.81 0 5.45 1.09 7.43 3.07a10.39 10.39 0 0 1 3.08 7.41c0 5.78-4.73 10.49-10.45 10.49Z" />
    </svg>
    <span class="whatsapp-support__text">@lang('WhatsApp Support')</span>
</a>
