<?= $this->extend('template') ?>

<?= $this->section('content') ?>
  <style>
    .register-box {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 40px;
      margin-top: 60px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .login-link { text-align: center; margin-top: 15px; }
  </style>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="register-box">
          <h2 class="text-center mb-4 text-primary">Create Account</h2>

          <?php if(session()->getFlashdata('success')): ?>
              <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>

          <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <?php if(isset($validation)): ?>
              <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
          <?php endif; ?>

          <form action="<?= base_url('register') ?>" method="post">
              <div class="mb-3">
                  <label>Name</label>
                  <input type="text" name="name" class="form-control" value="<?= old('name') ?>">
              </div>
              <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label>Password</label>
                      <input type="password" name="password" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                      <label>Confirm Password</label>
                      <input type="password" name="password_confirm" class="form-control">
                  </div>
              </div>
              <button type="submit" class="btn btn-primary w-100">Create Account</button>
          </form>

          <div class="login-link">
            <p class="mt-3">Already have an account? <a href="<?= base_url('login') ?>">Login here</a></p>
          </div>

        </div>
      </div>
    </div>
  </div>
<?= $this->endSection() ?>
