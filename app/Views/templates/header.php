<?php
$role = session('role');
$name = session('name');
$isLoggedIn = !empty($role);
?>

<div class="top-header d-flex align-items-center">
  <div class="logo">
    <h5 class="m-0 text-primary">Learning Management System</h5>
  </div>

  <div class="d-flex align-items-center ms-auto">
    <nav class="me-3">
      <ul class="nav">
        <?php if ($isLoggedIn): ?>
          <?php if ($role === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Admin Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/users') ?>">User Management</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('courses') ?>">Courses Management</a></li>
          <?php elseif ($role === 'teacher'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Teacher Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('courses') ?>">Courses</a></li>
            <li class="nav-item"><a class="nav-link" href="#">New Lesson</a></li>
          <?php elseif ($role === 'student'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Student Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= base_url('courses') ?>">My Courses</a></li>
          <?php endif; ?>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <?php if ($isLoggedIn): ?>
      <div class="me-3 position-relative">
        <a href="javascript:void(0)" id="notifToggle" class="text-decoration-none position-relative">
          <i class="fas fa-bell fa-lg"></i>
          <span id="notifBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="display:none;">0</span>
        </a>
        <div id="notifMenu" class="bg-white shadow rounded p-2" style="display:none; z-index:3000; position:fixed;">
          <div id="notifList"></div>
          <div class="text-end mt-2">
          </div>
        </div>
      </div>

      <div class="user-dropdown position-relative">
        <button class="dropdown-toggle">
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