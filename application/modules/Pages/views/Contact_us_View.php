<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<!-- HERO -->
<div class="hero-bg" style="background-image:url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')); ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Contact Us</h1>
    <p class="hero-descr">Home / Contact Us</p>
  </div>
</div>

<!-- CONTACT SECTION -->
<div class="container contact-section">
  <div class="row gy-4">

    <!-- FORM -->
    <div class="col-12 col-lg-7">
      <div class="card p-4 h-100 shadow-sm">
        <h5 class="contact-title mb-4">Send us an email</h5>

        <form action="<?= base_url('Pages/About_us/Createcontactus'); ?>" method="POST">
          <div class="row g-3">

            <div class="col-12">
              <label class="form-label">Name *</label>
              <input type="text" name="FullName" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" name="Email" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Phone *</label>
              <input type="tel" name="PhoneNumber" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label">Subject</label>
              <input type="text" name="Subject" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label">Message *</label>
              <textarea name="Message" rows="4" class="form-control" required></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-paper-plane me-2"></i> Send Message
              </button>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- CONTACT INFO -->
    <div class="col-12 col-lg-5">
      <div class="card p-4 h-100 shadow-sm">
        <h5 class="contact-title mb-4">Contact Info</h5>

        <p><i class="bi bi-geo-alt-fill"></i>
          <strong>Address:</strong><br>
          <?= e($this->Model->get_setting('site_address', 'Bujumbura, Burundi'))?>
        </p>

        <p><i class="bi bi-telephone-fill"></i>
          <strong>Phone:</strong><br>
          <?= e($this->Model->get_setting('site_phone', '+25700000000'))?>
        </p>

        <p><i class="bi bi-envelope-fill"></i>
          <strong>Email:</strong><br>
          <?= e($this->Model->get_setting('site_email', 'contact@example.com'))?>
        </p>
      </div>
    </div>

  </div>
</div>

<!-- TESTIMONIAL -->
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-6">
      <div class="card p-4 p-md-5 shadow">
        <h5 class="contact-title text-center mb-4">Laissez votre témoignage</h5>

        <form action="<?= base_url('Pages/Contact_us/Create'); ?>" method="POST" enctype="multipart/form-data">
          <div class="row g-3">

            <div class="col-md-6">
              <input type="text" name="name" class="form-control" placeholder="Nom complet" required>
            </div>

            <div class="col-md-6">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="col-12">
              <input type="text" name="role" class="form-control" placeholder="Profession / Fonction">
            </div>

            <div class="col-12">
              <textarea name="message" class="form-control" rows="4" placeholder="Votre témoignage" required></textarea>
            </div>

            <!-- RATING -->
            <div class="col-12 text-center">
              <div class="rating-stars">
                <i class="star" data-value="1">★</i>
                <i class="star" data-value="2">★</i>
                <i class="star" data-value="3">★</i>
                <i class="star" data-value="4">★</i>
                <i class="star" data-value="5">★</i>
              </div>
              <input type="hidden" name="rating" id="ratingValue" value="0">
            </div>

            <div class="col-12">
              <input type="file" name="photo" class="form-control">
            </div>

            <div class="col-12">
              <button class="btn btn-primary w-100">Envoyer mon témoignage</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- CSS -->
<style>
.hero-bg {
  position: relative;
  min-height: 40vh;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
}
.hero-bg::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.45);
}
.hero-body {
  position: relative;
  color: #fff;
}
.hero-tete {
  font-size: clamp(2rem, 4vw, 3rem);
}
.contact-section {
  padding: 60px 15px;
}
.contact-title {
  font-weight: 700;
  text-transform: uppercase;
}
.rating-stars .star {
  font-size: 1.8rem;
  color: #ccc;
  cursor: pointer;
}
.rating-stars .star.active {
  color: #ffc107;
}
</style>

<!-- JS -->
<script>
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('ratingValue');

stars.forEach(star => {
  star.addEventListener('click', () => {
    let rating = star.dataset.value;
    ratingInput.value = rating;

    stars.forEach(s => {
      s.classList.toggle('active', s.dataset.value <= rating);
    });
  });
});
</script>

<?php include VIEWPATH.'media/Footer.php'; ?>
