<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Notifications</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Notifications</span>
      </div>
    </div>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <table id="notificationsTable" class="table bordered-table mb-0" style="width:100%">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Message</th>
              <th>Type</th>
              <th>Date</th>
              <th>Statut</th>
              <th style="width:120px">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
$(function() {
    const table = $('#notificationsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: BASE_URL + 'api/notifications',
            type: 'GET',
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'titre' },
            { data: 'message' },
            {
                data: 'type',
                render: function(data) {
                    const badges = { 'info': 'bg-info', 'success': 'bg-success', 'warning': 'bg-warning', 'danger': 'bg-danger', 'error': 'bg-danger' };
                    return '<span class="badge ' + (badges[data] || 'bg-secondary') + ' px-24 py-4 radius-4 fw-medium text-sm">' + (data || '-') + '</span>';
                }
            },
            {
                data: 'cree_le',
                render: function(data) {
                    if (!data) return '-';
                    const d = new Date(data);
                    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                }
            },
            {
                data: 'lu',
                render: function(data) {
                    return data ? '<span class="badge bg-success-100 text-success-600 px-24 py-4 radius-4 fw-medium text-sm">Lu</span>' : '<span class="badge bg-warning-100 text-warning-600 px-24 py-4 radius-4 fw-medium text-sm">Non lu</span>';
                }
            },
            {
                data: 'uuid',
                orderable: false,
                render: function(data, type, row) {
                    if (!row.lu) {
                        return '<button class="btn btn-sm btn-primary-600" onclick="markAsRead(\'' + data + '\')"><i class="ri-check-line"></i> Marquer lu</button>';
                    }
                    return '<span class="text-secondary-light">-</span>';
                }
            }
        ],
        order: [[3, 'desc']],
        language: { url: BASE_URL + 'assets/js/lib/datatables-fr.json' }
    });

    window.markAsRead = function(id) {
        API.notifications.markRead(id).then(function(r) {
            if (r.success) table.ajax.reload();
            else Swal.fire('Erreur', r.message, 'error');
        });
    };
});
</script>