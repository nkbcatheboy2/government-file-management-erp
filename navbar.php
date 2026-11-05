<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm" style="background: linear-gradient(135deg, #1E3A8A, #172554) !important;">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fa-solid fa-landmark me-2"></i>GOV FILE ERP
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#erpNav">
      <span class="navbar-toggler-nav"></span>
    </button>
    <div class="collapse navbar-collapse" id="erpNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active fw-bold' : '' ?>" href="index.php">
            <i class="fa-solid fa-chart-pie me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'file_register.php' ? 'active fw-bold' : '' ?>" href="file_register.php">
            <i class="fa-solid fa-folder-open me-1"></i> File Register
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'movement_log.php' ? 'active fw-bold' : '' ?>" href="movement_log.php">
            <i class="fa-solid fa-arrows-split-up-and-left me-1"></i> Movement Log
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'master_data.php' ? 'active fw-bold' : '' ?>" href="master_data.php">
            <i class="fa-solid fa-database me-1"></i> Master Data
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <a href="file_register.php?action=add" class="btn btn-warning btn-sm fw-semibold">
          <i class="fa-solid fa-plus-circle me-1"></i> + Register New File
        </a>
        <form action="logout.php" method="POST" class="m-0">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-outline-light btn-sm">Log out</button>
        </form>
      </div>
    </div>
  </div>
</nav>
