<?php
$role = session('role');
$name = session('name');
$isLoggedIn = !empty($role);
?>

<div class="top-header d-flex align-items-center">
  <!-- Left: Logo -->
  <div class="logo">
    <h5 class="m-0 text-primary">Learning Management System</h5>
  </div>

  <!-- Right: Role-based nav + Dropdown -->
  <div class="d-flex align-items-center ms-auto">
    <nav class="me-3">
      <ul class="nav">
        <?php if ($isLoggedIn): ?>
          <?php if ($role === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Admin Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">User Management</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Courses Management</a></li>
          <?php elseif ($role === 'teacher'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Teacher Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
            <li class="nav-item"><a class="nav-link" href="#">New Lesson</a></li>
          <?php elseif ($role === 'student'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Student Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">My Grades</a></li>
          <?php endif; ?>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <?php if ($isLoggedIn): ?>
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
    <?php else: ?>
      <a class="btn btn-primary" href="<?= base_url('login') ?>">Log in</a>
    <?php endif; ?>
  </div>
</div>
