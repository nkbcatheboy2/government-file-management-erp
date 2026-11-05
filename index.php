<?php
require_once 'db.php';

// Metrics Calculation
$total_files = $conn->query("SELECT COUNT(*) as count FROM files")->fetch_assoc()['count'] ?? 0;
$pending_files = $conn->query("SELECT COUNT(*) as count FROM files WHERE current_status = 'Pending'")->fetch_assoc()['count'] ?? 0;
$disposed_files = $conn->query("SELECT COUNT(*) as count FROM files WHERE current_status = 'Disposed'")->fetch_assoc()['count'] ?? 0;
$urgent_files = $conn->query("SELECT COUNT(*) as count FROM files WHERE priority = 'Urgent' AND current_status != 'Disposed'")->fetch_assoc()['count'] ?? 0;

// Ageing Calculation for Pending files
$ageing_fresh = $conn->query("SELECT COUNT(*) as count FROM files WHERE current_status = 'Pending' AND DATEDIFF(CURDATE(), received_date) <= 7")->fetch_assoc()['count'] ?? 0;
$ageing_attention = $conn->query("SELECT COUNT(*) as count FROM files WHERE current_status = 'Pending' AND DATEDIFF(CURDATE(), received_date) BETWEEN 8 AND 30")->fetch_assoc()['count'] ?? 0;
$ageing_delayed = $conn->query("SELECT COUNT(*) as count FROM files WHERE current_status = 'Pending' AND DATEDIFF(CURDATE(), received_date) > 30")->fetch_assoc()['count'] ?? 0;

// Zone-wise Breakdown
$zone_stats = $conn->query("SELECT current_zone, COUNT(*) as pending_count FROM files WHERE current_status = 'Pending' GROUP BY current_zone ORDER BY current_zone ASC");
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | File Management ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .kpi-card { border: none; border-radius: 12px; color: white; }
        .bg-card-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .bg-card-amber { background: linear-gradient(135deg, #d97706, #b45309); }
        .bg-card-emerald { background: linear-gradient(135deg, #059669, #047857); }
        .bg-card-rose { background: linear-gradient(135deg, #e11d48, #be123c); }
        .bg-age-fresh { background-color: #ecfdf5; border-left: 4px solid #10b981; }
        .bg-age-warn { background-color: #fffbeb; border-left: 4px solid #f59e0b; }
        .bg-age-danger { background-color: #fef2f2; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid px-4 py-4">
    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card kpi-card bg-card-blue p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small fw-bold opacity-75">Total Registered</span>
                        <h2 class="fw-bold mb-0 mt-1"><?= $total_files ?></h2>
                        <a href="file_register.php" class="text-white text-decoration-underline small">View All Files →</a>
                    </div>
                    <i class="fa-solid fa-folder-open fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card bg-card-amber p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small fw-bold opacity-75">Pending Files</span>
                        <h2 class="fw-bold mb-0 mt-1"><?= $pending_files ?></h2>
                        <a href="file_register.php?status=Pending" class="text-white text-decoration-underline small">View Pending →</a>
                    </div>
                    <i class="fa-solid fa-clock-rotate-left fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card bg-card-emerald p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small fw-bold opacity-75">Disposed Files</span>
                        <h2 class="fw-bold mb-0 mt-1"><?= $disposed_files ?></h2>
                        <a href="file_register.php?status=Disposed" class="text-white text-decoration-underline small">View Completed →</a>
                    </div>
                    <i class="fa-solid fa-circle-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card bg-card-rose p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small fw-bold opacity-75">Urgent Priority</span>
                        <h2 class="fw-bold mb-0 mt-1"><?= $urgent_files ?></h2>
                        <a href="file_register.php?priority=Urgent" class="text-white text-decoration-underline small">Immediate Action →</a>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Ageing SLA Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-age-fresh rounded shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark">≤ 7 Days (Fresh)</strong>
                    <div class="text-muted small">Within Normal SLA</div>
                </div>
                <h4 class="fw-bold text-success mb-0"><?= $ageing_fresh ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-age-warn rounded shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark">8 – 30 Days (Attention)</strong>
                    <div class="text-muted small">Action Required</div>
                </div>
                <h4 class="fw-bold text-warning mb-0"><?= $ageing_attention ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-age-danger rounded shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark">> 30 Days (Delayed)</strong>
                    <div class="text-muted small">Escalation Required</div>
                </div>
                <h4 class="fw-bold text-danger mb-0"><?= $ageing_delayed ?></h4>
            </div>
        </div>
    </div>

    <!-- Zone-wise Pending Table & Quick Links -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-location-dot me-2"></i>Zone-wise Pending Summary</h6>
                <table class="table table-bordered table-sm">
                    <thead class="table-light"><tr><th>Zone</th><th>Pending Files</th></tr></thead>
                    <tbody>
                        <?php if ($zone_stats && $zone_stats->num_rows > 0): ?>
                            <?php while ($z = $zone_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($z['current_zone']) ?></td>
                                    <td class="fw-bold text-danger"><?= $z['pending_count'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted">No pending files across zones.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-info me-2"></i>System Modules Guide</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>File Register:</strong> Register new files and view the list of active files.</span>
                        <a href="file_register.php" class="btn btn-outline-primary btn-sm">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>Movement Log:</strong> Track file movements between officers and zones.</span>
                        <a href="movement_log.php" class="btn btn-outline-primary btn-sm">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>Master Data:</strong> View the fixed lists of zones, sections, and officers.</span>
                        <a href="master_data.php" class="btn btn-outline-primary btn-sm">Open</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>