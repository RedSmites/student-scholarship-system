document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    var list = document.getElementById('requirement-list');
    var template = document.getElementById('requirement-template');
    var addButton = document.getElementById('add-requirement');
    if (!list || !template || !addButton) {
        return;
    }

    function updateRequirementNumbers() {
        list.querySelectorAll('.requirement-number').forEach(function (number, index) {
            number.textContent = String(index + 1);
        });
    }

    addButton.addEventListener('click', function () {
        list.appendChild(template.content.cloneNode(true));
        updateRequirementNumbers();
    });

    list.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-requirement')) {
            event.target.closest('.requirement-item').remove();
            updateRequirementNumbers();
        }
    });

    updateRequirementNumbers();
});
