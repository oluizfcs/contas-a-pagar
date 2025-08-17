document.querySelectorAll('.input-wrapper').forEach(wrapper => {
    const field = wrapper.querySelector('input[maxlength], textarea[maxlength]');
    const counter = wrapper.querySelector('.char-counter');

    if (field && counter) {
        const max = field.getAttribute('maxlength');

        const updateCounter = () => {
            if (field.value.length > max/2) {
                counter.textContent = `${field.value.length}/${max}`;
            }
        };

        field.addEventListener('input', updateCounter);
        updateCounter(); // atualiza ao carregar
    }
});