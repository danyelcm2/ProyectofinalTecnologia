<?php

declare(strict_types=1);
$assetVersion = (string) @filemtime(__DIR__ . '/../../../assets/js/app.js');
?>
<?php if (!empty($_SESSION['is_authenticated'])): ?>
        </main>
    </div>
<?php endif; ?>
<div id="appToastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/app.js?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
