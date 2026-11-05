<?php
require_once 'db.php';

// Filter handling
$where = "WHERE 1=1";
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where .= " AND current_status = '$status'";
}
if (isset($_GET['priority']) && !empty($_GET['priority'])) {
    $priority = $conn->real_escape_string($_GET['priority']);
    $where .= " AND priority = '$priority'";
}

$files_result = $conn->query("SELECT *, DATEDIFF(CURDATE(), received_date) AS days_pending FROM files $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Register | File Management ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid px-4 py-4">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> File registered successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Top Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-folder-open text-primary me-2"></i>File Register List</h4>
            <p class="text-muted small mb-0">Total Files Found: <?= $files_result->num_rows ?></p>
        </div>
        <button class="btn btn-primary fw-semibold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addFileModal">
            <i class="fa-solid fa-circle-plus me-1"></i> + Add New File Record
        </button>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>File No.</th>
                        <th>Subject</th>
                        <th>Current Zone</th>
                        <th>Current Section</th>
                        <th>Officer</th>
                        <th>Received Date</th>
                        <th>Ageing</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($files_result && $files_result->num_rows > 0): ?>
                        <?php while ($row = $files_result->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= htmlspecialchars($row['file_no']) ?></td>
                                <td><?= htmlspecialchars($row['file_subject']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['current_zone']) ?></span></td>
                                <td><?= htmlspecialchars($row['current_section']) ?></td>
                                <td><i class="fa-regular fa-user me-1 text-muted"></i><?= htmlspecialchars($row['current_officer']) ?></td>
                                <td><?= htmlspecialchars($row['received_date']) ?></td>
                                <td>
                                    <?php if ($row['current_status'] == 'Disposed'): ?>
                                        <span class="text-muted small">Disposed</span>
                                    <?php else: ?>
                                        <span class="badge <?= $row['days_pending'] > 30 ? 'bg-danger' : ($row['days_pending'] > 7 ? 'bg-warning text-dark' : 'bg-success') ?>">
                                            <?= $row['days_pending'] ?> Days
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= $row['priority'] ?></span></td>
                                <td>
                                    <span class="badge <?= $row['current_status'] == 'Disposed' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= $row['current_status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No records available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add File Modal (Add New Record) -->
<div class="modal fade" id="addFileModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="save_file.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle me-2"></i>Register a New File</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">File Number *</label>
                            <input type="text" name="file_no" class="form-control" placeholder="e.g. GOV/2026/101" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">File Type</label>
                            <input type="text" name="file_type" class="form-control" placeholder="e.g. Establishment, Tender, Legal">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">File Subject *</label>
                            <input type="text" name="file_subject" class="form-control" placeholder="Enter the subject here..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Origin Zone</label>
                            <select name="origin_zone" class="form-select">
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="Zone <?= $i ?>">Zone <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Origin Section</label>
                            <select name="origin_section" class="form-select">
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <option value="Section <?= $i ?>">Section <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Zone *</label>
                            <select name="current_zone" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="Zone <?= $i ?>">Zone <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Section *</label>
                            <select name="current_section" class="form-select" required>
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <option value="Section <?= $i ?>">Section <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Officer *</label>
                            <select name="current_officer" class="form-select" required>
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <option value="Officer <?= $i ?>">Officer <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Received Date *</label>
                            <input type="date" name="received_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="Normal">Normal</option>
                                <option value="Important">Important</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="current_status" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Additional remarks..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-save me-1"></i> Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($_GET['action']) && $_GET['action'] == 'add'): ?>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('addFileModal'));
    myModal.show();
</script>
<?php endif; ?>
</body>
</html>
