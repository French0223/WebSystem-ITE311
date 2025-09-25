<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - ITE311-LABASA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .top-header {
      background: white;
      padding: 15px 40px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .logo h4 {
      color: #0d6efd;
      font-weight: 600;
    }
    .user-dropdown {
      position: relative;
    }
    .dropdown-toggle {
      background: #0d6efd;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
    }
    .dropdown-toggle:hover {
      background: #0b5ed7;
    }
    .dropdown-menu-custom {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background: white;
      min-width: 160px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 6px;
      z-index: 1000;
      margin-top: 5px;
    }
    .dropdown-menu-custom.show {
      display: block;
    }
    .dropdown-item-custom {
      padding: 10px 15px;
      color: #333;
      text-decoration: none;
      display: block;
    }
    .dropdown-item-custom:hover {
      background-color: #f8f9fa;
      color: #0d6efd;
      text-decoration: none;
    }
    .dashboard-content {
      padding: 40px 0;
    }
    .welcome-message h2 {
      color: #0d6efd;
      font-weight: 600;
    }
    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-left: 4px solid #0d6efd;
      margin-bottom: 20px;
    }
    .stat-icon {
      font-size: 1.8rem;
      color: #0d6efd;
      margin-bottom: 10px;
    }
    .stat-number {
      font-size: 1.5rem;
      font-weight: bold;
      color: #333;
    }
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }
    .content-card {
      background: white;
      border-radius: 8px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .section-title {
      color: #0d6efd;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .section-title i {
      margin-right: 10px;
    }
    .quick-action-btn {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 6px;
      padding: 15px;
      text-decoration: none;
      color: #333;
      display: block;
      margin-bottom: 10px;
    }
    .quick-action-btn:hover {
      background: #0d6efd;
      color: white;
      text-decoration: none;
    }
    .activity-item {
      padding: 15px 0;
      border-bottom: 1px solid #eee;
    }
    .activity-item:last-child {
      border-bottom: none;
    }
  </style>
</head>
<body class="bg-light">

  <!-- Top Header -->
  <div class="top-header d-flex align-items-center">
  <!-- Left: Logo -->
  <div class="logo">
    <h4 class="m-0">Learning Management System</h4>
  </div>

  <!-- Right: Nav + Dropdown together -->
  <div class="d-flex align-items-center ms-auto">
    <!-- Role-based nav -->
    <?php $role = session('role'); ?>
    <nav>
      <ul class="nav">
        <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php/admin/dashboard') ?>">Admin Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">User Management</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Courses Management</a></li>
        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php/teacher/dashboard') ?>">Teacher Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="#">New Lesson</a></li>
        <?php elseif ($role === 'student'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php/student/dashboard') ?>">Student Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="#">My Grades</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- Dropdown -->
    <div class="user-dropdown ms-3">
      <button class="dropdown-toggle" onclick="toggleDropdown()">
        <?= $user['name'] ?>
      </button>
      <div class="dropdown-menu-custom" id="userDropdown">
        <a href="<?= base_url('index.php/logout') ?>" class="dropdown-item-custom">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>
  </div>
</div>

  <!-- Dashboard Content -->
  <div class="container dashboard-content">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Welcome Message -->
    <div class="welcome-message">
      <h2>Welcome back, <?= $user['name'] ?>!</h2>
      <p class="text-muted">Here's what's happening with your account today.</p>
    </div>

    <?php
      // Wrapper: load role-specific partials
      $role = $user['role'] ?? session('role');
      $name = $user['name'] ?? session('name');

      switch ($role) {
        case 'admin':
          echo view('admin', ['name' => $name]);
          break;
        case 'teacher':
          echo view('teacher', ['name' => $name]);
          break;
        case 'student':
          echo view('student', ['name' => $name]);
          break;
        default:
          echo '<div class="alert alert-warning mt-3">Role not recognized.</div>';
          break;
      }
    ?>
  </div>

  <script>
    function toggleDropdown() {
      document.getElementById("userDropdown").classList.toggle("show");
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
      if (!event.target.matches('.dropdown-toggle')) {
        var dropdowns = document.getElementsByClassName("dropdown-menu-custom");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
          }
        }
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
