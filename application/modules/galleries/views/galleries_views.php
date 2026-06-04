<?php 
$imgCount = 0; $vidCount = 0; $linkCount = 0;
foreach($galleries as $g) {
    if ($g['TypeMedia'] == 'image') $imgCount++;
    elseif ($g['TypeMedia'] == 'video') $vidCount++;
    elseif ($g['TypeMedia'] == 'link') $linkCount++;
}
?>
<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="page-wrapper">
<div class="page-content">

<div class="page-breadcrumb d-flex align-items-center mb-3">
  <div class="breadcrumb-title pe-3">Admin</div>
  <div class="ps-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><i class="bx bx-home-alt"></i></li>
      <li class="breadcrumb-item active">Galerie</li>
    </ol>
  </div>
  <div class="ms-auto">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGallery">
      <i class="bx bx-plus"></i> Nouveau média
    </button>
  </div>
</div>

<?= e($this->session->flashdata('sms'))?>

<div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
  <div class="col">
    <div class="card bg-primary bg-gradient text-white shadow-sm border-0">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bx bx-image-alt fs-1"></i>
        <div>
          <h5 class="card-title mb-0"><?= e($imgCount) ?></h5>
          <small>Images</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card bg-danger bg-gradient text-white shadow-sm border-0">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bx bx-video fs-1"></i>
        <div>
          <h5 class="card-title mb-0"><?= e($vidCount) ?></h5>
          <small>Vidéos</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card bg-success bg-gradient text-white shadow-sm border-0">
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bx bx-link-alt fs-1"></i>
        <div>
          <h5 class="card-title mb-0"><?= e($linkCount) ?></h5>
          <small>Liens</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
<div class="card-body">
<div class="table-responsive">

<table id="example" class="table table-hover align-middle">
<thead class="table-dark">
<tr>
  <th>#</th>
  <th>Média</th>
  <th>Type</th>
  <th>Description</th>
  <th class="text-center">Actions</th>
</tr>
</thead>

<tbody>
<?php $i=1; foreach($galleries as $g): ?>
<tr>
  <td class="fw-bold"><?= e($i++ )?></td>

  <td class="text-center" style="width:120px">
    <?php if($g['TypeMedia']=='image'): ?>
      <div class="position-relative d-inline-block">
        <img src="<?= base_url('attachments/gallery/'.$g['Media']) ?>"
             class="rounded shadow-sm border media-preview"
             style="width:70px; height:70px; object-fit:cover; cursor:pointer;"
             data-full="<?= base_url('attachments/gallery/'.$g['Media']) ?>"
             data-title="<?= e(strip_tags($g['Description'])) ?>">
      </div>

    <?php elseif($g['TypeMedia']=='video'): ?>
      <div class="position-relative d-inline-block">
        <video width="100" height="70" style="object-fit:cover" class="rounded shadow-sm border" muted>
          <source src="<?= base_url('attachments/gallery/'.$g['Media']) ?>">
        </video>
        <span class="position-absolute top-50 start-50 translate-middle text-white fs-4 opacity-75">
          <i class="bx bx-play-circle"></i>
        </span>
      </div>

    <?php else: ?>
      <div class="d-flex align-items-center justify-content-center" style="height:70px">
        <a href="<?= e($g['Media'] )?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
          <i class="bx bx-link-external"></i> Ouvrir
        </a>
      </div>
    <?php endif; ?>
  </td>

  <td>
    <?php if($g['TypeMedia']=='image'): ?>
      <span class="badge bg-primary rounded-pill px-3 py-1">IMAGE</span>
    <?php elseif($g['TypeMedia']=='video'): ?>
      <span class="badge bg-danger rounded-pill px-3 py-1">VIDEO</span>
    <?php else: ?>
      <span class="badge bg-success rounded-pill px-3 py-1">LIEN</span>
    <?php endif; ?>
  </td>

  <td class="text-muted"><?= strip_tags($g['Description']) ?></td>

  <td class="text-center">
    <div class="btn-group">
      <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#edit<?= e($g['IdGallery'] )?>" title="Modifier">
        <i class="bx bx-edit"></i>
      </button>
      <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= e($g['IdGallery'] )?>" title="Supprimer">
        <i class="bx bx-trash"></i>
      </button>
    </div>
  </td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>
</div>
</div>

</div>
</div>

<!-- ================= MODALS ================= -->

<?php foreach($galleries as $g): ?>

<!-- EDIT -->
<div class="modal fade" id="edit<?= e($g['IdGallery'] )?>" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" action="<?= base_url('galleries/update') ?>" enctype="multipart/form-data">

<div class="modal-header">
  <h5 class="modal-title">Modifier média</h5>
  <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="uuid" value="<?= e($g['uuid'] )?>">
<input type="hidden" name="HiddenMedia" value="<?= e($g['Media'] )?>">

<div class="mb-3">
  <label>Type</label>
  <select name="TypeMedia" class="form-control media-type" required>
    <option value="image" <?= e($g['TypeMedia']=='image'?'selected':'' )?>>Image</option>
    <option value="video" <?= e($g['TypeMedia']=='video'?'selected':'' )?>>Vidéo</option>
    <option value="link"  <?= e($g['TypeMedia']=='link'?'selected':'' )?>>Lien</option>
  </select>
</div>

<div class="mb-3 text-center">
  <?php if($g['TypeMedia']=='image'): ?>
    <img src="<?= base_url('attachments/gallery/'.$g['Media']) ?>"
         class="rounded shadow-sm border"
         style="max-width:200px; max-height:120px; object-fit:cover;">
  <?php elseif($g['TypeMedia']=='video'): ?>
    <video width="200" height="120" style="object-fit:cover" class="rounded shadow-sm border" controls muted>
      <source src="<?= base_url('attachments/gallery/'.$g['Media']) ?>">
    </video>
  <?php else: ?>
    <a href="<?= e($g['Media'] )?>" target="_blank" class="btn btn-outline-primary">
      <i class="bx bx-link-external"></i> <?= e($g['Media'] )?>
    </a>
  <?php endif; ?>
  <small class="d-block text-muted mt-1">Média actuel</small>
</div>

<div class="mb-3 media-file">
  <label>Nouveau fichier (laisser vide pour conserver l'actuel)</label>
  <input type="file" name="Media" class="form-control">
</div>

<div class="mb-3 media-link">
  <label>Nouveau lien</label>
  <input type="text" name="Link" class="form-control" value="<?= e($g['Media'] )?>">
</div>

<div class="mb-3">
  <label>Description</label>
  <textarea name="Description" class="form-control" rows="4"><?= e($g['Description'] )?></textarea>
</div>

</div>

<div class="modal-footer">
  <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
  <button type="submit" class="btn btn-primary">Enregistrer</button>
</div>

</form>
</div>
</div>
</div>

<!-- DELETE -->
<div class="modal fade" id="delete<?= e($g['IdGallery'] )?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="post" action="<?= base_url('galleries/delete') ?>">
<input type="hidden" name="uuid" value="<?= e($g['uuid'] )?>">

<div class="modal-header">
  <h5 class="modal-title text-danger">Confirmation</h5>
</div>

<div class="modal-body">
  Voulez-vous vraiment supprimer ce média ?
</div>

<div class="modal-footer">
  <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
  <button type="submit" class="btn btn-danger">Supprimer</button>
</div>

</form>
</div>
</div>
</div>

<?php endforeach; ?>

<!-- ADD -->
<div class="modal fade" id="addGallery">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" action="<?= base_url('galleries/create') ?>" enctype="multipart/form-data">

<div class="modal-header">
  <h5 class="modal-title">Ajouter un média</h5>
</div>

<div class="modal-body">

<div class="mb-3">
  <label>Type</label>
  <select name="TypeMedia" class="form-control media-type" required>
    <option value="">-- Choisir --</option>
    <option value="image">Image</option>
    <option value="video">Vidéo</option>
    <option value="link">Lien</option>
  </select>
</div>

<div class="mb-3 media-file">
  <label>Fichier</label>
  <input type="file" name="Media" class="form-control">
</div>

<div class="mb-3 media-link">
  <label>Lien</label>
  <input type="text" name="Link" class="form-control">
</div>

<div class="mb-3">
  <label>Description</label>
  <textarea name="Description" class="form-control" rows="4"></textarea>
</div>

<div class="mb-3 progress-video" style="display:none">
  <label>Progression</label>
  <div class="progress" style="height:25px">
    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
  </div>
</div>

</div>

<div class="modal-footer">
  <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
  <button type="submit" class="btn btn-success">Enregistrer</button>
</div>

</form>
</div>
</div>
</div>

<!-- JS -->
<script>
const CHUNK_SIZE = 1.5 * 1024 * 1024; // 1.5MB per chunk

document.querySelectorAll('.modal').forEach(modal => {
  const type = modal.querySelector('.media-type');
  if (!type) return;

  const file = modal.querySelector('.media-file');
  const link = modal.querySelector('.media-link');

  function toggle(){
    if(type.value === 'link'){
      file.style.display = 'none';
      link.style.display = 'block';
    }else{
      file.style.display = 'block';
      link.style.display = 'none';
    }
  }

  type.addEventListener('change', toggle);
  toggle();
});

/* ---- Chunked upload for videos (fetch / AJAX only) ---- */
const CREATE_URL = '<?= base_url('galleries/create') ?>';
const CHUNK_URL  = '<?= base_url('galleries/upload_chunk') ?>';

document.querySelectorAll('form[action*="galleries/create"]').forEach(form => {
  const fileInput = form.querySelector('input[name="Media"]');
  const typeSelect = form.querySelector('.media-type');
  const descInput = form.querySelector('textarea[name="Description"]');
  const submitBtn = form.querySelector('button[type="submit"]');
  const progressWrap = form.querySelector('.progress-video');
  const progressBar = progressWrap ? progressWrap.querySelector('.progress-bar') : null;

  form.addEventListener('submit', async function(e) {
    const selectedType = typeSelect ? typeSelect.value : '';
    if (selectedType !== 'video') return;
    e.preventDefault();

    if (!submitBtn) { alert('Bouton introuvable'); return; }

    try {
      const file = fileInput.files[0];
      if (!file) { alert('Sélectionnez un fichier vidéo.'); return; }

      const identifier = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
      const description = descInput ? descInput.value : '';

      submitBtn.disabled = true;
      submitBtn.textContent = 'Upload...';
      if (progressWrap) progressWrap.style.display = 'block';

      for (let i = 0; i < totalChunks; i++) {
        const start = i * CHUNK_SIZE;
        const end = Math.min(start + CHUNK_SIZE, file.size);
        const blob = file.slice(start, end);

        const fd = new FormData();
        fd.append('chunk', blob, 'chunk.bin');
        fd.append('identifier', identifier);
        fd.append('chunkIndex', i);
        fd.append('totalChunks', totalChunks);
        fd.append('filename', file.name);

        const resp = await fetch(CHUNK_URL, { method: 'POST', body: fd });
        if (!resp.ok) { alert('Erreur serveur upload (HTTP ' + resp.status + ')'); break; }
        const data = await resp.json();

        if (data.status === 'done') {
          if (progressBar) { progressBar.style.width = '100%'; progressBar.textContent = '100%'; }
          submitBtn.textContent = 'Finalisation...';

          const body = new FormData();
          body.append('ajax', '1');
          body.append('TypeMedia', 'video');
          body.append('Description', description);
          body.append('Media', data.filename);

          const createResp = await fetch(CREATE_URL, { method: 'POST', body });
          if (!createResp.ok) { alert('Erreur création (HTTP ' + createResp.status + ')'); break; }
          const createData = await createResp.json();

          if (createData.success) {
            location.reload();
          } else {
            alert(createData.error || 'Erreur création.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Erreur';
          }
          return;
        }

        const pct = Math.round(((i + 1) / totalChunks) * 100);
        if (progressBar) { progressBar.style.width = pct + '%'; progressBar.textContent = pct + '%'; }
        submitBtn.textContent = 'Upload ' + (i + 1) + '/' + totalChunks;
      }
    } catch(err) {
      alert('Erreur réseau: ' + err.message);
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Erreur'; }
    }
  });
});

/* ---- Lightbox pour images ---- */
document.querySelectorAll('.media-preview').forEach(img => {
  img.addEventListener('click', function() {
    const src = this.getAttribute('data-full') || this.src;
    const lightbox = document.getElementById('lightboxModal');
    if (!lightbox) return;
    lightbox.querySelector('img').src = src;
    lightbox.querySelector('.modal-title').textContent = this.getAttribute('data-title') || '';
    new bootstrap.Modal(lightbox).show();
  });
});
</script>

<!-- Lightbox modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title text-white"></h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img src="" class="img-fluid rounded shadow" style="max-height:80vh;">
      </div>
    </div>
  </div>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>