<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($title ?? 'Learning Management System') ?></title>
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
      body { background-color: #f8f9fa; }
      .top-header { background: #fff; padding: 12px 20px; box-shadow: 0 2px 4px rgba(0,0,0,.06); }
      .nav .nav-link { color: #0d6efd; }
      .nav .nav-link:hover { color: #0b5ed7; }
      .dropdown-toggle { background: #0d6efd; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; }
      .dropdown-menu-custom { display:none; right:0; }
      .dropdown-menu-custom.show { display:block; }
    </style>
</head>
<body class="bg-light">

    <?= view('templates/header', ['title' => $title ?? 'LMS']) ?>

    <?php
      $session = session();
      $success = $session->getFlashdata('success');
      $error   = $session->getFlashdata('error');
      $warning = $session->getFlashdata('warning');
      $info    = $session->getFlashdata('info');
    ?>
    <?php if ($success || $error || $warning || $info): ?>
      <div class="container mt-3">
        <?php if ($success): ?>
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div><?= esc($success) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="assertive">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <div><?= esc($error) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($warning): ?>
          <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <div><?= esc($warning) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($info): ?>
          <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-info me-2"></i>
            <div><?= esc($info) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php $full = trim($this->renderSection('full_content')); ?>
    <?php if ($full !== ''): ?>
      <?= $full ?>
    <?php else: ?>
      <main class="container py-4">
          <?= $this->renderSection('content') ?>
      </main>
    <?php endif; ?>

    <script>
      function toggleDropdown() {
        var el = document.getElementById('userDropdown');
        if (el) el.classList.toggle('show');
      }
      window.addEventListener('click', function (event) {
        const btn = document.querySelector('.dropdown-toggle');
        const menu = document.getElementById('userDropdown');
        if (!btn || !menu) return;
        if (!btn.contains(event.target) && !menu.contains(event.target)) {
          menu.classList.remove('show');
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Auto-dismiss flash alerts after 4 seconds
      window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          document.querySelectorAll('.alert').forEach(function(el){
            try {
              var inst = bootstrap.Alert.getOrCreateInstance(el);
              inst.close();
            } catch(e) {}
          });
        }, 4000);
      });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
