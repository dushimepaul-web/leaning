<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-header">
    <h1 class="page-title">Notifications</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('Dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Notifications</li>
    </ol>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des notifications</h5>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="API.notifications.list().then(r => { if(r.success) location.reload(); })">
                        <i class="ri-refresh-line me-1"></i> Actualiser
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="notificationsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    const table = $('#notificationsTable').DataTable({
        processing: true,
        serverSide: true,
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
                    const badges = {
                        'info': 'bg-info',
                        'success': 'bg-success',
                        'warning': 'bg-warning',
                        'danger': 'bg-danger',
                        'error': 'bg-danger'
                    };
                    return '<span class="badge ' + (badges[data] || 'bg-secondary') + '">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'cree_le',
                render: function(data) {
                    return data ? moment(data).format('DD/MM/YYYY HH:mm') : '-';
                }
            },
            { 
                data: 'lu',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Lu</span>' : '<span class="badge bg-warning text-dark">Non lu</span>';
                }
            },
            { 
                data: 'id_notification',
                orderable: false,
                render: function(data, type, row) {
                    if (!row.lu) {
                        return '<button class="btn btn-sm btn-outline-primary" onclick="markAsRead(\'' + data + '\')"><i class="ri-check-line me-1"></i> Marquer lu</button>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            }
        ],
        order: [[3, 'desc']],
        language: {
            url: BASE_URL + 'assets/js/lib/datatables-fr.json'
        }
    });

    window.markAsRead = function(id) {
        API.notifications.markRead(id).then(r => {
            if (r.success) {
                table.ajax.reload();
            } else {
                Swal.fire('Erreur', r.message, 'error');
            }
        });
    };
});
</script>