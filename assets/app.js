document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('img[data-fallback-image]').forEach((image) => {
        image.addEventListener('error', () => {
            if (image.src !== image.dataset.fallbackImage) {
                image.src = image.dataset.fallbackImage;
            }
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || 'Потвърждавате ли действието?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-validate]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const invalid = [];

            form.querySelectorAll('[required]').forEach((field) => {
                if (!field.value.trim()) {
                    invalid.push('Попълнете всички задължителни полета.');
                }
            });

            form.querySelectorAll('input[type="number"]').forEach((field) => {
                if (Number(field.value) < 0) {
                    invalid.push('Числовите стойности не могат да бъдат отрицателни.');
                }
            });

            if (invalid.length > 0) {
                event.preventDefault();
                alert([...new Set(invalid)].join('\n'));
            }
        });
    });

    document.querySelectorAll('[data-image-preview-input]').forEach((input) => {
        const form = input.closest('form');
        const preview = form ? form.querySelector('[data-image-preview]') : null;

        if (!preview) {
            return;
        }

        input.addEventListener('input', () => {
            const value = input.value.trim();

            if (value) {
                preview.src = value;
            }
        });
    });
});
