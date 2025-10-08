<?= $this->extend('template') ?>

<?= $this->section('content') ?>
  <style>
    .content-box {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 40px;
      margin-top: 40px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="content-box mt-5">
          <h1 class="mb-3">Contact Us</h1>
          <p class="lead">You can reach us through the following contact details:</p>
          <ul class="list-unstyled">
            <li><strong>Email:</strong> support@ite311labasa.com</li>
            <li><strong>Phone:</strong> +63 912 345 6789</li>
            <li><strong>Address:</strong> RMMC Campus, General Santos City</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?= $this->endSection() ?>
