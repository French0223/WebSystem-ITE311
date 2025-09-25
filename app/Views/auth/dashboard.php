<?= view('templates/header', ['title' => 'Dashboard']) ?>

  <!-- Dashboard Content -->
  <div class="container dashboard-content">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Welcome Message -->


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