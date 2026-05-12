const slider = document.querySelector('[data-slider]');

if (slider) {
    const track = slider.querySelector('[data-slider-track]');
    const slides = Array.from(track.querySelectorAll('.slide-card'));
    const previousButton = slider.querySelector('[data-slider-prev]');
    const nextButton = slider.querySelector('[data-slider-next]');
    const dotsWrap = document.querySelector('[data-slider-dots]');
    let activeIndex = Math.floor(slides.length / 2);

    const dots = slides.map((slide, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', `Show movie ${index + 1}`);
        dot.addEventListener('click', () => {
            activeIndex = index;
            updateSlider();
        });
        dotsWrap.appendChild(dot);

        return dot;
    });

    function updateSlider() {
        slides.forEach((slide, index) => {
            const offset = index - activeIndex;
            slide.classList.toggle('is-active', index === activeIndex);
            slide.style.setProperty('--offset', offset);
            slide.style.setProperty('--abs-offset', Math.abs(offset));
        });

        dots.forEach((dot, index) => {
            dot.classList.toggle('is-active', index === activeIndex);
        });
    }

    previousButton.addEventListener('click', () => {
        activeIndex = activeIndex === 0 ? slides.length - 1 : activeIndex - 1;
        updateSlider();
    });

    nextButton.addEventListener('click', () => {
        activeIndex = activeIndex === slides.length - 1 ? 0 : activeIndex + 1;
        updateSlider();
    });

    window.addEventListener('resize', updateSlider);
    updateSlider();
}
