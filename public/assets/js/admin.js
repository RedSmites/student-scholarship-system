(function () {
    var toggle = document.getElementById('admin-sidebar-toggle');
    var backdrop = document.getElementById('admin-sidebar-backdrop');
    if (!toggle || !backdrop) return;
    var mobile = window.matchMedia('(max-width: 991.98px)');
    var closeDrawer = function () {
        document.body.classList.remove('admin-sidebar-open');
        toggle.setAttribute('aria-expanded', 'false');
    };
    toggle.addEventListener('click', function () {
        if (mobile.matches) {
            document.body.classList.toggle('admin-sidebar-open');
            toggle.setAttribute('aria-expanded', document.body.classList.contains('admin-sidebar-open') ? 'true' : 'false');
        } else {
            var collapsed = document.body.classList.toggle('admin-sidebar-collapsed');
            toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            try { localStorage.setItem('scholarship-admin-sidebar-collapsed', collapsed ? '1' : '0'); } catch (error) {}
        }
    });
    backdrop.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeDrawer(); });
    mobile.addEventListener('change', closeDrawer);
    if (!mobile.matches) {
        try {
            if (localStorage.getItem('scholarship-admin-sidebar-collapsed') === '1') {
                document.body.classList.add('admin-sidebar-collapsed');
                toggle.setAttribute('aria-expanded', 'false');
            }
        } catch (error) {}
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    var modalElement = document.getElementById('admin-action-modal');
    var modalTitle = document.getElementById('admin-action-modal-title');
    var modalMessage = document.getElementById('admin-action-modal-message');
    var modalIcon = document.getElementById('admin-action-modal-icon');
    var modalConfirm = document.getElementById('admin-action-modal-confirm');
    var actionModal = modalElement && window.bootstrap ? new bootstrap.Modal(modalElement) : null;
    var pendingAction = null;

    function askForConfirmation(options, callback) {
        if (!actionModal) {
            if (window.confirm(options.message)) callback();
            return;
        }
        modalTitle.textContent = options.title || 'Confirm action';
        modalMessage.textContent = options.message || 'Are you sure you want to continue?';
        modalConfirm.textContent = options.button || 'Confirm';
        var isDanger = options.variant === 'danger';
        modalConfirm.classList.toggle('btn-danger', isDanger);
        modalConfirm.classList.toggle('btn-primary', !isDanger);
        modalIcon.textContent = isDanger ? '!' : '?';
        modalIcon.classList.toggle('is-danger', isDanger);
        pendingAction = callback;
        actionModal.show();
    }

    if (modalConfirm) {
        modalConfirm.addEventListener('click', function () {
            var action = pendingAction;
            pendingAction = null;
            actionModal.hide();
            if (action) action();
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return;
            }
            event.preventDefault();
            var submitter = event.submitter;
            askForConfirmation({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirm,
                button: form.dataset.confirmButton,
                variant: form.dataset.confirmVariant
            }, function () {
                form.dataset.confirmed = 'true';
                if (submitter && submitter.form === form) form.requestSubmit(submitter);
                else form.requestSubmit();
            });
        });
    });

    var list = document.getElementById('requirement-list');
    var template = document.getElementById('requirement-template');
    var addButton = document.getElementById('add-requirement');
    if (!list || !template || !addButton) return;

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
        var button = event.target.closest('.remove-requirement');
        if (!button) return;
        var item = button.closest('.requirement-item');
        askForConfirmation({
            title: 'Remove document requirement?',
            message: 'Remove this document requirement from the scholarship form?',
            button: 'Remove requirement',
            variant: 'danger'
        }, function () {
            item.remove();
            updateRequirementNumbers();
        });
    });

    updateRequirementNumbers();
});
