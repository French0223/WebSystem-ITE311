<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Announcements</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>
    <div class="d-flex justify-content-end mb-3">
      <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
    <h1 class="mb-4">Announcements</h1>
    <?php if (empty($announcements)): ?>
      <div class="alert alert-info">No announcements yet.</div>
    <?php else: ?>
      <div class="list-group">
        <?php foreach ($announcements as $a): ?>
          <div class="list-group-item">
            <h5 class="mb-1"><?= esc($a['title']) ?></h5>
            <p class="mb-1"><?= esc($a['content']) ?></p>
            <small class="text-muted">
              Posted: <?= isset($a['created_at']) ? esc($a['created_at']) : '—' ?>
            </small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>