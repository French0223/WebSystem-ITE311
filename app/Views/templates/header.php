<?php

$role = session('role');
$name = session('name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= esc($title ?? 'LMS') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    .top-header { background: #fff; padding: 12px 20px; box-shadow: 0 2px 4px rgba(0,0,0,.06); }
    .nav .nav-link { color: #0d6efd; }
    .nav .nav-link:hover { color: #0b5ed7; }
    .dropdown-toggle { background: #0d6efd; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; }
    .dropdown-menu-custom { display:none; right:0; }
    .dropdown-menu-custom.show { display:block; }
  </style>
</head>
<body class="bg-light">

<div class="top-header d-flex align-items-center">
  <!-- Left: Logo -->
  <div class="logo">
    <h5 class="m-0 text-primary">Learning Management System</h5>
  </div>

  <!-- Right: Role-based nav + Dropdown -->
  <div class="d-flex align-items-center ms-auto">
    <nav class="me-3">
      <ul class="nav">
        <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">User Management</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Courses Management</a></li>
        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Teacher Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="#">New Lesson</a></li>
        <?php elseif ($role === 'student'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('student/dashboard') ?>">Student Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Grades</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="user-dropdown position-relative">
      <button class="dropdown-toggle" onclick="toggleDropdown()">
        <?= esc($name ?? 'User') ?>
      </button>
      <div class="dropdown-menu-custom position-absolute bg-white shadow rounded p-2" id="userDropdown">
        <a href="<?= base_url('logout') ?>" class="dropdown-item text-decoration-none d-block px-2 py-1">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleDropdown() {
    document.getElementById('userDropdown').classList.toggle('show');
  }
  
  // Close dropdown when clicking outside
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
