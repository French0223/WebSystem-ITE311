<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ITE311-LABASA - Contact</title>
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

    .content-box {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 40px;
      margin-top: 40px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="bg-light">

  <!-- Top Navigation (same as Home) -->
  <div class="top-nav d-flex justify-content-between align-items-center">
    <div class="logo">
      <h4 class="m-0">Learning Management System</h4>
    </div>
    <div class="d-flex align-items-center">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("/") ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("about") ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active fw-bold" href="<?= site_url("contact") ?>">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("login") ?>">Log in</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Content Section -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
