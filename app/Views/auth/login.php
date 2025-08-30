<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - ITE311-LABASA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Top navbar */
    .top-nav {
      background: white;
      padding: 15px 40px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .top-nav .nav-link {
      color: #333 !important;
      font-weight: 500;
      margin-left: 15px;
    }
    .top-nav .nav-link.active {
      color: #0d6efd !important;
      font-weight: 600;
    }

    /* Content box styling */
    .login-box {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 40px;
      margin-top: 60px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>
<body class="bg-light">

  <!-- Top Navigation -->
  <div class="top-nav d-flex justify-content-between align-items-center">
    <div class="logo">
      <h4 class="m-0">Learning Management System</h4>
    </div>
    <div class="d-flex align-items-center">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url() ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('index.php/about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('index.php/contact') ?>">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="<?= base_url('index.php/login') ?>">Log in</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Login Content -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="login-box">
          <h2 class="text-center mb-4 text-primary">Login</h2>

          <?php if(session()->getFlashdata('success')): ?>
              <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>

          <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <?php if(isset($validation)): ?>
              <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
          <?php endif; ?>

          <form action="<?= base_url('index.php/login') ?>" method="post">
              <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
              </div>
              <div class="mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control">
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>

          <div class="register-link">
            <p class="mt-3">Don't have an account? <a href="<?= base_url('index.php/register') ?>">Register here</a></p>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
