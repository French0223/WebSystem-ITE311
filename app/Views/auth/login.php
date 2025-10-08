<?= $this->extend('template') ?>

<?= $this->section('content') ?>
  <style>
    .login-box {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 40px;
      margin-top: 60px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .register-link { text-align: center; margin-top: 15px; }
  </style>

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

          <form action="<?= base_url('login') ?>" method="post">
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
            <p class="mt-3">Don't have an account? <a href="<?= base_url('register') ?>">Register here</a></p>
          </div>

        </div>
      </div>
    </div>
  </div>
<?= $this->endSection() ?>
