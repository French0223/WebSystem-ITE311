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
</body>
</html>
