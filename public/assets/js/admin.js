(function () {
    var toggle = document.getElementById('admin-sidebar-toggle');
    var backdrop = document.getElementById('admin-sidebar-backdrop');
    if (!toggle || !backdrop) return;
    var mobile = window.matchMedia('(max-width: 991.98px)');
    var closeDrawer = function () { document.body.classList.remove('admin-sidebar-open'); };
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
