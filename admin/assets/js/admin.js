document.addEventListener('DOMContentLoaded', function () {
    var shell = document.getElementById('adminShell');
    var toggleBtn = document.getElementById('sidebarToggle');

    // Restore collapsed state
    if (localStorage.getItem('kaswa_sidebar_collapsed') === '1') {
        shell.classList.add('collapsed');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            shell.classList.toggle('collapsed');
            localStorage.setItem('kaswa_sidebar_collapsed', shell.classList.contains('collapsed') ? '1' : '0');
        });
    }

    // Confirm before destructive actions
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });

    // Image preview on file input
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var preview = document.querySelector(input.getAttribute('data-preview'));
            if (preview && input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) { preview.src = e.target.result; };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });

    // Multi-image preview grid (product photo uploads)
    document.querySelectorAll('input[type=file][data-preview-multi]').forEach(function (input) {
        input.addEventListener('change', function () {
            var grid = document.querySelector(input.getAttribute('data-preview-multi'));
            if (!grid || !input.files || !input.files.length) {
                return;
            }
            grid.innerHTML = '';
            Array.prototype.forEach.call(input.files, function (file) {
                if (!file.type.match('image.*')) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width:90px; height:90px; object-fit:cover; background:var(--paper); border:1px solid var(--steel-light); border-radius:8px;';
                    grid.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    });
});
