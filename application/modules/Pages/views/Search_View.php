<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Recherche</h1>
    <p class="hero-descr">Résultats pour "<?= e($query) ?>"</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center mb-4">
      <div class="col-md-6">
        <form action="<?= base_url('Pages/Home/search') ?>" method="GET" class="d-flex">
          <input class="form-control me-2" type="search" name="q" value="<?= e($query) ?>" placeholder="Rechercher...">
          <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
      </div>
    </div>

    <h4 class="mb-4"><?= count($results['courses']) + count($results['posts']) + count($results['galleries']) ?> résultat(s)</h4>

    <?php if (!empty($results['courses'])): ?>
      <h5 class="text-primary mb-3"><i class="bi bi-book"></i> Cours</h5>
      <div class="row g-3 mb-4">
        <?php foreach ($results['courses'] as $c): ?>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h6 class="card-title"><?= e($c['nom_course']) ?></h6>
                <p class="card-text small text-muted"><?= e(substr(strip_tags($c['description']), 0, 120)) ?>...</p>
                <a href="<?= base_url('Pages/Home/coursedetail/'.$c['uuid']) ?>" class="btn btn-sm btn-outline-primary">Voir</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($results['posts'])): ?>
      <h5 class="text-success mb-3"><i class="bi bi-file-text"></i> Articles</h5>
      <div class="row g-3 mb-4">
        <?php foreach ($results['posts'] as $p): ?>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h6 class="card-title"><?= e($p['title']) ?></h6>
                <p class="card-text small text-muted"><?= e(substr(strip_tags($p['details']), 0, 120)) ?>...</p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($results['galleries'])): ?>
      <h5 class="text-warning mb-3"><i class="bi bi-images"></i> Galerie</h5>
      <div class="row g-3 mb-4">
        <?php foreach ($results['galleries'] as $g): ?>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <?php if ($g['TypeMedia'] == 'image'): ?>
                <img src="<?= base_url('attachments/gallery/'.$g['Media']) ?>" class="card-img-top" style="height:180px;object-fit:cover;">
              <?php endif; ?>
              <div class="card-body">
                <span class="badge bg-<?= $g['TypeMedia']=='image'?'primary':($g['TypeMedia']=='video'?'danger':'success') ?> mb-2"><?= e($g['TypeMedia']) ?></span>
                <p class="card-text small text-muted"><?= e(strip_tags($g['Description'])) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (empty($results['courses']) && empty($results['posts']) && empty($results['galleries'])): ?>
      <div class="text-center py-5">
        <i class="bi bi-search fs-1 text-muted"></i>
        <p class="text-muted mt-2">Aucun résultat trouvé pour "<?= e($query) ?>"</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include VIEWPATH.'media/Footer.php'; ?>
