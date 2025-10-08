<?= $this->extend('template') ?>

<?= $this->section('full_content') ?>
  <style>
    .hero-section {
      position: relative;
      min-height: calc(100vh - 70px);
      display: flex;
      align-items: center;
      justify-content: flex-start; 
      padding: 40px 80px; 
      overflow: hidden;
    }
    .hero-slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease-in-out; }
    .hero-slide.active { opacity: 1; }
    .hero-section::before { content: ""; position: absolute; inset: 0; background: rgba(25, 3, 70, 0.7); z-index: 1; }
    .hero-box { position: relative; z-index: 2; padding: 40px; border-radius: 8px; max-width: 600px; text-align: left; }
    h1 { font-size: 2rem; font-weight: bold; color: #fff; }
    p { color: #fff; }
  </style>

  <div class="hero-section">
    <div class="hero-slide active" style="background-image: url('https://i.ytimg.com/vi/EQQclSX18Vg/maxresdefault.jpg');"></div>
    <div class="hero-slide" style="background-image: url('https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.6435-9/72416281_10157327393661346_5503501463084597248_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeFnvqV7s6hmEuUP4Pg2Ig-kcoVDpa99ANlyhUOlr30A2VsR0j5JA_PvkuzORl0t-WP1tew5a2TnnypIDgrry4tP&_nc_ohc=keUIqgGwXGMQ7kNvwE_J5ls&_nc_oc=Adm8JQDYyGd2J-dEkQpsYEr8Jz-LO6FrXTqmPnrPwVn-7uJ6BjVD5toqW4Rn-XIV1Vw4qmiCTzsPxF4Pg3ZiXer0&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&_nc_gid=ygChj6rodOlt8znFONqeow&oh=00_AfUOly28v0_S6qGVvHwSk__3XmWEQnSn33c7exAX-j6uZg&oe=68D7F862');"></div>
    <div class="hero-slide" style="background-image: url('https://rmmc.edu.ph/public/images/library-three.png');"></div>

    <div class="hero-box">
      <h1>ITE311-LABASA</h1>
      <p>
        The Learning Management System provides tools to deliver engaging contents 
        and activities for online learning.
      </p>
    </div>
  </div>

  <script>
    const slides = document.querySelectorAll('.hero-slide');
    let index = 0;
    setInterval(() => {
      slides[index].classList.remove('active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('active');
    }, 4000);
  </script>
<?= $this->endSection() ?>
