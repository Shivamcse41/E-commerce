document.addEventListener("DOMContentLoaded", function(event) {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('toggled');
            if(content) content.classList.toggle('toggled');
        });
    }

    // Auto-dismiss alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000);
    });
});
