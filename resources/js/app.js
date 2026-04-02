import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    registerModals();
    registerTabs();
    registerTableSearch();
    registerFoodSearch();
    registerFoodPicker();
    registerProfileCalculator();
});

function registerModals() {
    const openButtons = document.querySelectorAll('[data-modal-open]');
    const closeButtons = document.querySelectorAll('[data-modal-close]');

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(`[data-modal="${button.dataset.modalOpen}"]`);

            if (! modal) {
                return;
            }

            modal.hidden = false;

            const meal = button.dataset.meal;
            const title = modal.querySelector('.portal-modal__header h2');

            if (meal && title) {
                title.textContent = `Add Food to ${meal.toLowerCase()}`;
            }

            if (meal) {
                modal.querySelectorAll('input[name="meal_slot"]').forEach((input) => {
                    input.value = meal;
                });
            }
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            // Don't close modal if the button is inside a form that's being submitted
            if (button.closest('form')) {
                return;
            }

            const modal = button.closest('[data-modal]');

            if (modal) {
                modal.hidden = true;
            }
        });
    });

    // Prevent modal closing when forms with data-modal-no-close are submitted
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form.hasAttribute('data-modal-no-close')) {
            // Allow the form to submit normally but don't close the modal
            // The page will redirect after submission, which will close the modal anyway
            return;
        }
    });

    // Add global deleteMeal function
    window.deleteMeal = function(entryId, mealSlot) {
        if (confirm(`Delete this ${mealSlot.toLowerCase()} meal?`)) {
            document.getElementById(`delete-form-${entryId}`).submit();
        }
    };

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('[data-modal]').forEach((modal) => {
            modal.hidden = true;
        });
    });
}

function registerTabs() {
    document.querySelectorAll('[data-tab-group]').forEach((group) => {
        const buttons = group.querySelectorAll('[data-tab-target]');
        const panels = group.parentElement?.querySelectorAll('[data-tab-panel]') ?? [];
        const copyButton = group.parentElement?.querySelector('[data-tab-button-copy]');

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                buttons.forEach((item) => item.classList.remove('is-active'));
                button.classList.add('is-active');

                panels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel.dataset.tabPanel === button.dataset.tabTarget);
                });

                if (copyButton && group.dataset.tabGroup === 'content-tabs') {
                    copyButton.textContent = button.dataset.tabTarget === 'meal-plans' ? 'Add Meal Plan' : 'Add Food Item';
                }
            });
        });
    });
}

function registerTableSearch() {
    document.querySelectorAll('[data-table-search]').forEach((input) => {
        input.addEventListener('input', () => {
            const table = document.getElementById(input.dataset.tableSearch);

            if (! table) {
                return;
            }

            const term = input.value.trim().toLowerCase();

            table.querySelectorAll('tbody tr').forEach((row) => {
                row.hidden = ! row.textContent.toLowerCase().includes(term);
            });
        });
    });
}

function registerFoodSearch() {
    const searchInput = document.querySelector('[data-food-search]');

    if (! searchInput) {
        return;
    }

    searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();

        document.querySelectorAll('[data-food-name]').forEach((item) => {
            item.hidden = ! item.dataset.foodName.includes(term);
        });
    });
}

function registerFoodPicker() {
    const input = document.querySelector('[data-food-picker-input]');
    const hiddenInput = document.querySelector('[data-food-picker-hidden]');
    const datalist = document.getElementById('food-log-food-options');

    if (! input || ! hiddenInput || ! datalist) {
        return;
    }

    const options = Array.from(datalist.querySelectorAll('option'));

    const syncSelectedFood = () => {
        const selectedOption = options.find((option) => option.value.toLowerCase() === input.value.trim().toLowerCase());

        hiddenInput.value = selectedOption?.dataset.foodId ?? '';
    };

    input.addEventListener('input', syncSelectedFood);
    input.addEventListener('change', syncSelectedFood);

    if (! input.value && options[0]) {
        input.value = options[0].value;
        hiddenInput.value = options[0].dataset.foodId ?? '';
    }
}

function registerProfileCalculator() {
    const form = document.querySelector('[data-profile-form]');

    if (! form) {
        return;
    }

    const heightInput = form.querySelector('[data-profile-height]');
    const weightInput = form.querySelector('[data-profile-weight]');
    const targetInput = form.querySelector('[data-profile-target]');
    const bmiScore = document.querySelector('[data-bmi-score]');
    const bmiStatus = document.querySelector('[data-bmi-status]');
    const current = document.querySelector('[data-profile-current]');
    const currentInline = document.querySelector('[data-profile-current-inline]');
    const targetInline = document.querySelector('[data-profile-target-inline]');
    const remaining = document.querySelector('[data-profile-remaining]');
    const updateProfile = () => {
        const height = Number.parseFloat(heightInput?.value ?? '0');
        const weight = Number.parseFloat(weightInput?.value ?? '0');
        const target = Number.parseFloat(targetInput?.value ?? '0');

        if (! height || ! weight || ! target) {
            return;
        }

        const bmi = weight / ((height / 100) * (height / 100));
        const bmiRounded = bmi.toFixed(1);
        const remainingWeight = Math.max(weight - target, 0).toFixed(1);

        if (bmiScore) {
            bmiScore.textContent = bmiRounded;
        }

        if (bmiStatus) {
            bmiStatus.textContent = bmi < 18.5 ? 'Underweight' : (bmi < 25 ? 'Normal' : (bmi < 30 ? 'Overweight' : 'Obese'));
        }

        if (current) {
            current.textContent = `${weight} kg`;
        }

        if (currentInline) {
            currentInline.textContent = `${weight} kg`;
        }

        if (targetInline) {
            targetInline.textContent = `${target} kg`;
        }

        if (remaining) {
            remaining.textContent = `${remainingWeight} kg remaining`;
        }
    };

    [heightInput, weightInput, targetInput].forEach((input) => {
        input?.addEventListener('input', updateProfile);
    });
}
