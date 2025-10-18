<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-light bg-white border-bottom">
    <div class="container d-flex justify-content-between">
      <span class="navbar-brand mb-0 h6 text-primary">Learning Management System</span>
      <div>
        <span class="me-3 text-muted small"><?= esc(session('name')) ?> (<?= esc(session('role')) ?>)</span>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>
  <div class="container py-5">
    <h1 class="display-6">Welcome, Admin!</h1>
    <p class="text-muted mb-0">This is a placeholder dashboard for the admin role.</p>
  </div>
</body>
</html>
