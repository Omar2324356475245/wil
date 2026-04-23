</div> <!-- container -->
<footer class="mt-5 py-4 bg-dark text-white">
    <div class="container text-center">
        <p class="mb-0"><i class="bi bi-trophy"></i> Class Football Tournament &copy; <?php echo date('Y'); ?></p>
        <p class="mb-0"><small>Developed with <i class="bi bi-heart-fill text-danger"></i></small></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
</script>
</body>
</html>
