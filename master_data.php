<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <title>Master Data | File Management ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body style="background-color: #f4f6fa;">

<?php include 'navbar.php'; ?>

<div class="container-fluid px-4 py-4">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-database text-primary me-2"></i>Master Configuration Data</h4>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="fw-bold text-primary">Zones (8 Zones)</h6>
                <ul class="list-group list-group-flush small">
                    <?php for($i=1;$i<=8;$i++): ?><li class="list-group-item">Zone <?= $i ?></li><?php endfor; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="fw-bold text-primary">Sections (30 Sections)</h6>
                <div style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush small">
                        <?php for($i=1;$i<=30;$i++): ?><li class="list-group-item">Section <?= $i ?></li><?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="fw-bold text-primary">Officers (30 Officers)</h6>
                <div style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush small">
                        <?php for($i=1;$i<=30;$i++): ?><li class="list-group-item">Officer <?= $i ?></li><?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="fw-bold text-primary">Status & Priority</h6>
                <span class="badge bg-success mb-1">Disposed</span>
                <span class="badge bg-warning text-dark mb-1">Pending</span>
                <span class="badge bg-info text-dark mb-3">In Progress</span>
                <hr>
                <span class="badge bg-danger mb-1">Urgent Priority</span>
                <span class="badge bg-warning text-dark mb-1">Important</span>
                <span class="badge bg-secondary mb-1">Normal</span>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
