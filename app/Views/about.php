<?= $this->extend('template') ?>

<?= $this->section('content') ?>
  <style>
    .about-section { padding: 60px 20px; }
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }
    .card-custom:hover { transform: translateY(-5px); }
    .card-custom h3 { color: #4e54c8; font-weight: bold; }
  </style>

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
<?= $this->endSection() ?>
