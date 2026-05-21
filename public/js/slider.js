// Find the movie slider section on the home page.
const slider = document.querySelector('[data-slider]');

// Run slider code only if the slider exists on this page.
if (slider) {
    // Get the slider parts from the page.
    const track = slider.querySelector('[data-slider-track]');
    const slides = Array.from(track.querySelectorAll('.slide-card'));
    const previousButton = slider.querySelector('[data-slider-prev]');
    const nextButton = slider.querySelector('[data-slider-next]');
    const dotsWrap = document.querySelector('[data-slider-dots]');
    // Start from the middle card so the slider opens with a centered movie.
    let activeIndex = Math.floor(slides.length / 2);

    // Create one dot button for each slide.
    const dots = slides.map((slide, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', `Show movie ${index + 1}`);
        // Clicking a dot changes the active movie.
        dot.addEventListener('click', () => {
            activeIndex = index;
            updateSlider();
        });
        dotsWrap.appendChild(dot);

        return dot;
    });

    function updateSlider() {
        // Update every slide position based on the active middle card.
        slides.forEach((slide, index) => {
            const offset = index - activeIndex;
            slide.classList.toggle('is-active', index === activeIndex);
            slide.style.setProperty('--offset', offset);
            slide.style.setProperty('--abs-offset', Math.abs(offset));
        });

        // Highlight the active dot.
        dots.forEach((dot, index) => {
            dot.classList.toggle('is-active', index === activeIndex);
        });
    }

    // Previous button moves one slide backward and loops to the end.
    previousButton.addEventListener('click', () => {
        activeIndex = activeIndex === 0 ? slides.length - 1 : activeIndex - 1;
        updateSlider();
    });

    // Next button moves one slide forward and loops to the beginning.
    nextButton.addEventListener('click', () => {
        activeIndex = activeIndex === slides.length - 1 ? 0 : activeIndex + 1;
        updateSlider();
    });

    // Recalculate positions when the browser size changes.
    window.addEventListener('resize', updateSlider);
    // Initial setup when the page first loads.
    updateSlider();
}
