(function () {
  var base = window.BASE_URL;
  if (!base) {
    var current = document.currentScript;
    if (current && current.src) base = current.src.replace(/assets\/js\/library\.js(\?.*)?$/, '');
  }
  base = (base || '/').replace(/\/?$/, '/');

  function esc(value) {
    if (value === null || value === undefined) return '';
    return String(value).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function money(value) {
    var n = Number(value || 0);
    return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + (window.DEVISE || 'BIF');
  }

  function number(value) {
    return Number(value || 0).toLocaleString('fr-FR');
  }

  function today() {
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  async function api(path, data, method) {
    method = method || (data ? 'POST' : 'GET');
    var opt = { method: method, headers: { 'Accept': 'application/json' } };
    if (data && method !== 'GET') {
      opt.headers['Content-Type'] = 'application/json';
      opt.body = JSON.stringify(data);
    }
    var res = await fetch(base + path.replace(/^\//, ''), opt);
    var json = null;
    try { json = await res.json(); } catch (e) { throw new Error('Reponse invalide du serveur'); }
    if (!res.ok || json.success === false) throw new Error(json.message || 'Erreur');
    return json;
  }

  function toast(icon, title) {
    if (window.Swal) {
      Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 3200, timerProgressBar: true });
    }
  }

  function confirmAction(title, text) {
    if (!window.Swal) return Promise.resolve(confirm(title));
    return Swal.fire({
      icon: 'warning', title: title, text: text || '', showCancelButton: true,
      confirmButtonText: 'Confirmer', cancelButtonText: 'Annuler'
    }).then(function (r) { return !!r.isConfirmed; });
  }

  function badge(statut) {
    var map = {
      brouillon: 'secondary', validee: 'success', valide: 'success', annulee: 'danger', annule: 'danger',
      en_attente: 'warning', payee: 'success', partielle: 'warning', credit: 'info', retournee: 'danger',
      demande: 'warning', refusee: 'danger', remboursee: 'info', prete: 'info', distribuee: 'success'
    };
    return '<span class="badge bg-' + (map[statut] || 'secondary') + '-100 text-' + (map[statut] || 'secondary') + '-600">' + esc(statut || '-') + '</span>';
  }

  function combo(select, placeholder) {
    if (!select || select.dataset.comboReady) return;
    select.dataset.comboReady = '1';
    var wrap = document.createElement('div');
    wrap.className = 'lib-combo';
    wrap.style.position = 'relative';
    select.parentNode.insertBefore(wrap, select);
    wrap.appendChild(select);
    select.classList.add('d-none');
    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.placeholder = placeholder || 'Tapez pour rechercher...';
    input.autocomplete = 'off';
    var menu = document.createElement('div');
    menu.className = 'list-group position-absolute w-100 shadow';
    menu.style.zIndex = '2000';
    menu.style.maxHeight = '240px';
    menu.style.overflowY = 'auto';
    menu.style.display = 'none';
    wrap.appendChild(input);
    wrap.appendChild(menu);
    function label() {
      var o = select.options[select.selectedIndex];
      return o && o.value ? o.text : '';
    }
    function render(q) {
      var all = Array.from(select.options);
      var opts = all.filter(function (o) {
        return o.value !== '' && (!q || o.text.toLowerCase().indexOf(q.toLowerCase()) !== -1);
      });
      menu.innerHTML = opts.length ? opts.map(function (o) {
        return '<button type="button" class="list-group-item list-group-item-action py-2" data-idx="' + all.indexOf(o) + '">' + esc(o.text) + '</button>';
      }).join('') : '<div class="list-group-item text-secondary-light">Aucun resultat</div>';
      menu.style.display = 'block';
    }
    input.addEventListener('focus', function () { input.value = ''; render(''); });
    input.addEventListener('input', function () { render(this.value); });
    menu.addEventListener('mousedown', function (e) {
      var b = e.target.closest('[data-idx]');
      if (!b) return;
      e.preventDefault();
      var o = select.options[Number(b.dataset.idx)];
      select.value = o.value;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      input.value = o.text;
      menu.style.display = 'none';
    });
    input.addEventListener('blur', function () {
      setTimeout(function () { menu.style.display = 'none'; input.value = label(); }, 150);
    });
    select.addEventListener('change', function () { input.value = label(); });
    new MutationObserver(function () { input.value = label(); }).observe(select, { childList: true });
    input.value = label();
  }

  function downloadCSV(filename, headers, rows) {
    var lines = [headers.map(function (h) { return '"' + String(h).replace(/"/g, '""') + '"'; }).join(',')];
    rows.forEach(function (row) {
      lines.push(row.map(function (v) { return '"' + String(v === null || v === undefined ? '' : v).replace(/"/g, '""') + '"'; }).join(','));
    });
    var blob = new Blob(['\uFEFF' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
  }

  window.Library = {
    base: base, esc: esc, money: money, number: number, today: today,
    api: api, toast: toast, confirmAction: confirmAction, badge: badge, combo: combo, downloadCSV: downloadCSV
  };
})();
