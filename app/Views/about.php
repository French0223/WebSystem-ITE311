<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ITE311-LABASA - About</title>
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

    /* About Content */
    .about-section {
      padding: 60px 20px;
    }

    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }

    .card-custom:hover {
      transform: translateY(-5px);
    }

    .card-custom h3 {
      color: #4e54c8;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Top Navigation -->
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
          <a class="nav-link active fw-bold" href="<?= site_url("about") ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("contact") ?>">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("login") ?>">Log in</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- About Section -->
  <div class="about-section container">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <h3>Our Mission</h3>
          <p>
            To deliver a modern platform that improves online education for both students and instructors,
            making learning more interactive, accessible, and engaging.
          </p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-custom p-4">
          <h3>Our Vision</h3>
          <p>
            To be a leading digital learning solution that empowers education through technology 
            and inspires future innovation in online learning.
          </p>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card card-custom p-4">
          <h3>About the System</h3>
          <p>
            ITE311-LABASA is a Learning Management System project built with CodeIgniter. 
            It provides tools and features that support both students and teachers, including
            content delivery, task management, and online collaboration.
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
