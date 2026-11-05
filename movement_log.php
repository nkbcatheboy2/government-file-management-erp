<?php
require_once 'db.php';

// Active registered files for the dropdown in modal
$files_list = $conn->query("SELECT id, file_no, file_subject, current_zone, current_section, current_officer FROM files WHERE current_status != 'Disposed' ORDER BY file_no ASC");

// Fetch Movement Logs with File Details
$movements = $conn->query("SELECT * FROM movements ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movement Log | File Management ERP</title>
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
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'moved'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> File movement recorded successfully and the file's current location has been updated!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Action Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-arrows-split-up-and-left text-primary me-2"></i>Movement Log</h4>
            <p class="text-muted small mb-0">Total Movements Logged: <?= $movements ? $movements->num_rows : 0 ?></p>
        </div>
        <button class="btn btn-primary fw-semibold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newMovementModal">
            <i class="fa-solid fa-paper-plane me-1"></i> + Record File Movement
        </button>
    </div>

    <!-- Movement Records Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Move ID</th>
                        <th>File No.</th>
                        <th>Date & Time</th>
                        <th>From (Zone / Officer)</th>
                        <th>To (Zone / Officer)</th>
                        <th>Action</th>
                        <th>Acknowledged?</th>
                        <th>Forwarded By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($movements && $movements->num_rows > 0): ?>
                        <?php while ($m = $movements->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-primary">#M-<?= str_pad($m['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($m['file_no']) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($m['movement_date']) ?></small></td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($m['from_zone'] ?? '-') ?></span>
                                    <div class="small text-secondary"><?= htmlspecialchars($m['from_officer'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($m['to_zone']) ?></span>
                                    <div class="small fw-semibold"><?= htmlspecialchars($m['to_officer']) ?></div>
                                </td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($m['action'] ?? 'Forwarded') ?></span></td>
                                <td>
                                    <?php if ($m['acknowledged'] == 'Yes'): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($m['forwarded_by'] ?? '-') ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($m['remarks'] ?? '-') ?></small></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No file movements have been recorded yet. Use the button above to record the first movement.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Record New Movement -->
<div class="modal fade" id="newMovementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="save_movement.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-paper-plane me-2"></i>Record File Movement / Transfer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Select File -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Select File to Move *</label>
                            <select name="file_id" id="file_select" class="form-select" required onchange="updateCurrentLocation()">
                                <option value="">-- Select a file --</option>
                                <?php if ($files_list && $files_list->num_rows > 0): ?>
                                    <?php while ($f = $files_list->fetch_assoc()): ?>
                                        <option value="<?= $f['id'] ?>" 
                                                data-fileno="<?= htmlspecialchars($f['file_no']) ?>"
                                                data-zone="<?= htmlspecialchars($f['current_zone']) ?>"
                                                data-section="<?= htmlspecialchars($f['current_section']) ?>"
                                                data-officer="<?= htmlspecialchars($f['current_officer']) ?>">
                                            <?= htmlspecialchars($f['file_no']) ?> - <?= htmlspecialchars($f['file_subject']) ?> (Current: <?= htmlspecialchars($f['current_officer']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="file_no" id="file_no_input">
                        </div>

                        <!-- Current Location (Auto-Filled) -->
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Current Zone (From)</label>
                            <input type="text" name="from_zone" id="from_zone" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Current Section (From)</label>
                            <input type="text" name="from_section" id="from_section" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Current Officer (From)</label>
                            <input type="text" name="from_officer" id="from_officer" class="form-control bg-light" readonly>
                        </div>

                        <hr class="my-2">
                        <div class="col-12"><strong class="text-primary"><i class="fa-solid fa-arrow-right me-1"></i> Destination Details (Transfer To)</strong></div>

                        <!-- New Location -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">To Zone *</label>
                            <select name="to_zone" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="Zone <?= $i ?>">Zone <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">To Section *</label>
                            <select name="to_section" class="form-select" required>
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <option value="Section <?= $i ?>">Section <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">To Officer *</label>
                            <select name="to_officer" class="form-select" required>
                                <?php for($i=1; $i<=30; $i++): ?>
                                    <option value="Officer <?= $i ?>">Officer <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Meta Info -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Action</label>
                            <select name="action" class="form-select">
                                <option value="Forwarded">Forwarded</option>
                                <option value="Returned with Query">Returned with Query</option>
                                <option value="Approved & Passed">Approved & Passed</option>
                                <option value="Under Review">Under Review</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Forwarded By</label>
                            <input type="text" name="forwarded_by" class="form-control" placeholder="e.g. Dispatch Clerk">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Acknowledged?</label>
                            <select name="acknowledged" class="form-select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Movement Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Remarks about the movement..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fa-solid fa-paper-plane me-1"></i> Submit Movement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateCurrentLocation() {
    const select = document.getElementById('file_select');
    const selectedOption = select.options[select.selectedIndex];
    
    document.getElementById('file_no_input').value = selectedOption.getAttribute('data-fileno') || '';
    document.getElementById('from_zone').value = selectedOption.getAttribute('data-zone') || '';
    document.getElementById('from_section').value = selectedOption.getAttribute('data-section') || '';
    document.getElementById('from_officer').value = selectedOption.getAttribute('data-officer') || '';
}
</script>
</body>
</html>