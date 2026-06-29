<div class="page-header">
    <h1 class="page-title">Évaluations</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('Dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Évaluations</li>
    </ol>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des évaluations</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-add-line me-1"></i> Nouvelle évaluation
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="evaluationsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Libellé</th>
                                <th>Matière</th>
                                <th>Classe</th>
                                <th>Période</th>
                                <th>Date</th>
                                <th>Note max</th>
                                <th>Coefficient</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dataBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="evalForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" name="libelle" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date_evaluation" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Classe *</label>
                            <select class="form-select" name="id_classe" required>
                                <option value="">Sélectionner</option>
                                <?php foreach($classes as $c): ?>
                                <option value="<?= $c['id_classe'] ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Matière *</label>
                            <select class="form-select" name="id_matiere" required>
                                <option value="">Sélectionner</option>
                                <?php foreach($matieres as $m): ?>
                                <option value="<?= $m['id_matiere'] ?>"><?= htmlspecialchars($m['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Période *</label>
                            <select class="form-select" name="id_periode" required>
                                <option value="">Sélectionner</option>
                                <?php foreach($periodes as $p): ?>
                                <option value="<?= $p['id_periode'] ?>"><?= htmlspecialchars($p['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Note maximale</label>
                            <input type="number" class="form-control" name="note_max" value="20" min="1" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coefficient</label>
                            <input type="number" class="form-control" name="coefficient" value="1" min="0.5" step="0.5">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEval()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
BASE_URL = '<?= base_url() ?>';

async function loadData() {
    const res = await API.evaluations.list();
    if (!res.success) { $('#dataBody').html('<tr><td colspan="9" class="text-center text-danger">Erreur</td></tr>'); return; }
    let rows = '';
    (res.data || []).forEach((e, i) => {
        rows += `<tr>
            <td>${i + 1}</td>
            <td>${e.libelle || '-'}</td>
            <td>${e.matiere || '-'}</td>
            <td>${e.classe || '-'}</td>
            <td>${e.periode || '-'}</td>
            <td>${e.date_evaluation || '-'}</td>
            <td>${e.note_max || 20}</td>
            <td>${e.coefficient || 1}</td>
            <td><button class="btn btn-sm btn-outline-danger" onclick="delEval('${e.uuid}')"><i class="ri-delete-bin-line"></i></button></td>
        </tr>`;
    });
    $('#dataBody').html(rows || '<tr><td colspan="9" class="text-center text-muted">Aucune évaluation</td></tr>');
}

async function saveEval() {
    const form = document.getElementById('evalForm');
    const data = Object.fromEntries(new FormData(form));
    const res = await API.evaluations.create(data);
    if (res.success) {
        Swal.fire('Succès', 'Évaluation créée', 'success');
        bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
        form.reset();
        loadData();
    } else {
        Swal.fire('Erreur', res.message || 'Erreur', 'error');
    }
}

async function delEval(id) {
    const r = await Swal.fire({ title: 'Confirmer?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer' });
    if (r.isConfirmed) {
        await API.evaluations.delete(id);
        loadData();
    }
}

loadData();
</script>