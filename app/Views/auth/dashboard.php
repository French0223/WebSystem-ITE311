<?= $this->extend('template') ?>

<?= $this->section('content') ?>
  <?php
    // Wrapper: load role-specific partials
    $role = $user['role'] ?? session('role');
    $name = $user['name'] ?? session('name');

    switch ($role) {
      case 'admin':
        echo view('admin', ['name' => $name]);
        break;
    case 'instructor': // legacy role label
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
<?= $this->endSection() ?>