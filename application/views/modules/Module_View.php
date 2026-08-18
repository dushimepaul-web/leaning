<div class="card">
  <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
    <h6 class="fw-semibold mb-0" id="moduleTitle">Liste</h6>
    <button type="button" class="btn btn-primary text-sm btn-sm" id="btnAddNew">
      <i class="ri-add-line"></i> Ajouter
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped" id="dataTable">
        <thead><tr id="tableHead"></tr></thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Ajouter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="mainForm">
          <div class="row" id="formFields"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary" id="btnSave">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
