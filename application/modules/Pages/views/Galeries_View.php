<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Galerie</h1>
    <p class="hero-descr">Home / Galerie</p>
  </div>
</div>

<section class="gallery-section py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Notre Galerie</h2>
      <p class="text-muted">Découvrez nos images, vidéos et liens</p>
    </div>

    <!-- Filtres -->
    <div class="text-center mb-4">
      <div class="btn-group flex-wrap" role="group">
        <button class="btn btn-outline-warning active filter-btn" data-filter="all">Tous</button>
        <button class="btn btn-outline-warning filter-btn" data-filter="image">Images</button>
        <button class="btn btn-outline-warning filter-btn" data-filter="video">Vidéos</button>
        <button class="btn btn-outline-warning filter-btn" data-filter="link">Liens</button>
      </div>
    </div>

    <!-- Grille -->
    <div class="row g-4" id="galleryGrid">
      <?php foreach($galleries as $g): ?>
        <div class="col-12 col-sm-6 col-lg-4 gallery-col" data-type="<?= e($g['TypeMedia']) ?>">

          <?php if ($g['TypeMedia'] == 'image'): ?>
            <div class="card border-0 shadow-sm gallery-card">
              <div class="position-relative overflow-hidden" style="border-radius:12px 12px 0 0;">
                <img src="<?= base_url('attachments/gallery/'.$g['Media']) ?>"
                     class="card-img-top gallery-img"
                     alt="<?= e(strip_tags($g['Description'])) ?>"
                     loading="lazy"
                     style="height:250px; object-fit:cover; cursor:pointer;"
                     onclick="openLightbox(this.src)">
                <div class="gallery-overlay d-flex align-items-center justify-content-center">
                  <i class="bi bi-arrows-fullscreen fs-3 text-white"></i>
                </div>
              </div>
              <div class="card-body text-center">
                <span class="badge bg-warning text-dark mb-2">IMAGE</span>
                <p class="card-text small text-muted mb-0"><?= e(strip_tags($g['Description'])) ?></p>
              </div>
            </div>

          <?php elseif ($g['TypeMedia'] == 'video'): ?>
            <div class="card border-0 shadow-sm gallery-card">
              <div class="position-relative overflow-hidden" style="border-radius:12px 12px 0 0;">
                <video class="card-img-top" controls preload="metadata"
                       style="height:250px; object-fit:cover; background:#000;">
                  <source src="<?= base_url('attachments/gallery/'.$g['Media']) ?>" type="video/mp4">
                </video>
              </div>
              <div class="card-body text-center">
                <span class="badge bg-danger mb-2">VIDEO</span>
                <p class="card-text small text-muted mb-0"><?= e(strip_tags($g['Description'])) ?></p>
              </div>
            </div>

          <?php elseif ($g['TypeMedia'] == 'link'): ?>
            <div class="card border-0 shadow-sm gallery-card">
              <div class="position-relative overflow-hidden" style="border-radius:12px 12px 0 0;">
                <div class="ratio ratio-16x9" style="background:#f8f9fa;">
                  <iframe src="<?= e($g['Media']) ?>" allowfullscreen loading="lazy"></iframe>
                </div>
              </div>
              <div class="card-body text-center">
                <span class="badge bg-success mb-2">LIEN</span>
                <p class="card-text small text-muted mb-0"><?= e(strip_tags($g['Description'])) ?></p>
                <a href="<?= e($g['Media']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2 rounded-pill">
                  <i class="bi bi-box-arrow-up-right"></i> Ouvrir
                </a>
              </div>
            </div>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Lightbox -->
<div class="modal fade" id="lightboxGal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0">
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" id="lightboxGalImg" class="img-fluid rounded shadow" style="max-height:85vh;">
      </div>
    </div>
  </div>
</div>

<style>
.gallery-card {
  border-radius:12px;
  transition: transform .3s, box-shadow .3s;
}
.gallery-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0,0,0,.15) !important;
}
.gallery-card .overflow-hidden {
  cursor:pointer;
}
.gallery-overlay {
  position:absolute; inset:0;
  background:rgba(0,0,0,.4);
  opacity:0;
  transition: opacity .3s;
}
.gallery-card:hover .gallery-overlay {
  opacity:1;
}
.gallery-img {
  transition: transform .4s;
}
.gallery-card:hover .gallery-img {
  transform: scale(1.08);
}
.filter-btn.active {
  background:#ff9900 !important;
  border-color:#ff9900 !important;
  color:#fff !important;
}
.filter-btn {
  transition: all .2s;
}
</style>

<script>
function openLightbox(src) {
  document.getElementById('lightboxGalImg').src = src;
  new bootstrap.Modal(document.getElementById('lightboxGal')).show();
}

/* Filtres */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('.gallery-col').forEach(col => {
      if (filter === 'all' || col.dataset.type === filter) {
        col.style.display = '';
      } else {
        col.style.display = 'none';
      }
    });
  });
});
</script>

<?php include VIEWPATH.'media/Footer.php'; ?>
