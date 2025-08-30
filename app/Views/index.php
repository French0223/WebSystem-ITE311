<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ITE311-LABASA - Home</title>
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

    .top-nav .btn {
      margin-left: 20px;
    }

    /* Hero section container */
    .hero-section {
      position: relative;
      min-height: calc(100vh - 70px);
      display: flex;
      align-items: center;
      justify-content: flex-start; 
      padding: 40px 80px; 
      overflow: hidden;
    }

    /* Slides (backgrounds) */
    .hero-slide {
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: center;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
    }
    .hero-slide.active {
      opacity: 1;
    }

    /* Overlay */
    .hero-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(25, 3, 70, 0.7);
      z-index: 1;
    }

    /* Content box */
    .hero-box {
      position: relative;
      z-index: 2;

      padding: 40px;
      border-radius: 8px;
      max-width: 600px;
      text-align: left;
    }

    h1 {
      font-size: 2rem;
      font-weight: bold;
      color: #fff;
    }

    p {
      color: #fff;
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
          <a class="nav-link active fw-bold" href="<?= site_url("/") ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url("about") ?>">About</a>
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

  <!-- Hero Section -->
  <div class="hero-section">
    <!-- Background slides -->
    <div class="hero-slide active" style="background-image: url('https://i.ytimg.com/vi/EQQclSX18Vg/maxresdefault.jpg');"></div>
    <div class="hero-slide" style="background-image: url('https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.6435-9/72416281_10157327393661346_5503501463084597248_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeFnvqV7s6hmEuUP4Pg2Ig-kcoVDpa99ANlyhUOlr30A2VsR0j5JA_PvkuzORl0t-WP1tew5a2TnnypIDgrry4tP&_nc_ohc=keUIqgGwXGMQ7kNvwE_J5ls&_nc_oc=Adm8JQDYyGd2J-dEkQpsYEr8Jz-LO6FrXTqmPnrPwVn-7uJ6BjVD5toqW4Rn-XIV1Vw4qmiCTzsPxF4Pg3ZiXer0&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&_nc_gid=ygChj6rodOlt8znFONqeow&oh=00_AfUOly28v0_S6qGVvHwSk__3XmWEQnSn33c7exAX-j6uZg&oe=68D7F862');"></div>
    <div class="hero-slide" style="background-image: url('https://rmmc.edu.ph/public/images/library-three.png');"></div>

    <!-- Content box -->
    <div class="hero-box">
      <h1>ITE311-LABASA</h1>
      <p>
        The Learning Management System provides tools to deliver engaging contents 
        and activities for online learning.
      </p>
    </div>
  </div>

  <!-- JS for slideshow -->
  <script>
    const slides = document.querySelectorAll(".hero-slide");
    let index = 0;

    setInterval(() => {
      slides[index].classList.remove("active");
      index = (index + 1) % slides.length;
      slides[index].classList.add("active");
    }, 4000);
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
