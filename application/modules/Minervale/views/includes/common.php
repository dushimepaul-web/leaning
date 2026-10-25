<script>
window.MV = {
  money: function (value) {
    var n = Number(value || 0);
    var devise = typeof DEVISE !== 'undefined' ? DEVISE : 'BIF';
    return n.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' ' + devise;
  },
  number: function (value) {
    return Number(value || 0).toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
  },
  esc: function (value) {
    return String(value === null || value === undefined ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  },
  labels: {
    impaye: 'Non paye',
    partiel: 'Paye partiellement',
    paye: 'Paye en totalite',
    exonere_partiel: 'Exonere partiellement',
    exonere_total: 'Exonere totalement',
    trop_percu: 'Trop-percu',
    solde: 'Solde',
    annule: 'Annule',
    en_attente: 'En attente',
    approuvee: 'Approuvee',
    refusee: 'Refusee',
    effectue: 'Effectue',
    active: 'Active',
    annulee: 'Annulee'
  },
  badge: function (status) {
    var map = {
      impaye: 'bg-danger-focus text-danger-main',
      partiel: 'bg-warning-focus text-warning-main',
      paye: 'bg-success-focus text-success-main',
      solde: 'bg-success-focus text-success-main',
      exonere_partiel: 'bg-info-focus text-info-main',
      exonere_total: 'bg-info-focus text-info-main',
      trop_percu: 'bg-warning-focus text-warning-main',
      annule: 'bg-neutral-200 text-secondary-light',
      en_attente: 'bg-warning-focus text-warning-main',
      approuvee: 'bg-success-focus text-success-main',
      refusee: 'bg-danger-focus text-danger-main',
      effectue: 'bg-success-focus text-success-main',
      active: 'bg-success-focus text-success-main',
      annulee: 'bg-neutral-200 text-secondary-light'
    };
    var label = MV.labels[status] || status || '-';
    return '<span class="badge radius-4 ' + (map[status] || 'bg-neutral-200 text-secondary-light') + '">' + MV.esc(label) + '</span>';
  },
  toast: function (icon, title) {
    Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 2600, timerProgressBar: true });
  },
  error: function (message) {
    Swal.fire({ icon: 'error', title: 'Erreur', text: message || 'Une erreur est survenue' });
  },
  confirm: function (title, text) {
    return Swal.fire({ icon: 'warning', title: title, text: text, showCancelButton: true, confirmButtonText: 'Confirmer', cancelButtonText: 'Annuler' });
  },
  formData: function (form) {
    var data = {};
    new FormData(form).forEach(function (value, key) { data[key] = value; });
    return data;
  },
  filterPeriodes: function (yearSelect, periodSelect) {
    if (!yearSelect || !periodSelect) return;
    var year = String(yearSelect.value || '');
    var current = periodSelect.value;
    var selectedStillVisible = false;
    Array.prototype.forEach.call(periodSelect.options, function (option) {
      var optionYear = option.getAttribute('data-annee');
      var visible = !optionYear || !year || optionYear === year;
      option.hidden = !visible;
      option.disabled = !visible;
      if (!visible && option.value === current) option.selected = false;
      if (visible && option.value === current && current !== '') selectedStillVisible = true;
    });
    if (current !== '' && !selectedStillVisible) periodSelect.value = '';
  }
};
</script>
