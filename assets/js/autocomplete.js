function autoRender(items, query, labelFn) {
  const q = (query || '').toLowerCase().trim();
  const matches = q ? items.filter(i => labelFn(i).toLowerCase().includes(q)) : items;
  if (!matches.length) return '<div class="list-group-item text-secondary-light text-center py-2 text-sm">Aucun résultat</div>';
  return matches.slice(0, 20).map(i =>
    `<button type="button" class="list-group-item list-group-item-action text-start py-1 px-3 border-0 border-bottom border-neutral-100 text-sm" data-id="${i.id}">${labelFn(i)}</button>`
  ).join('');
}

function autoSetup(inputId, hiddenId, resultsId, items, labelFn) {
  const input = document.getElementById(inputId);
  const hidden = document.getElementById(hiddenId);
  const results = document.getElementById(resultsId);
  if (!input) return;

  function show() {
    if (!hidden.value) results.innerHTML = autoRender(items, input.value, labelFn);
    results.style.display = 'block';
  }

  input.addEventListener('focus', show);
  input.addEventListener('input', function() {
    hidden.value = '';
    this.classList.remove('border-success', 'border-2');
    results.innerHTML = autoRender(items, this.value, labelFn);
    results.style.display = 'block';
  });
  input.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      const btns = results.querySelectorAll('button');
      if (!btns.length) return; e.preventDefault();
      let idx = Array.from(btns).indexOf(document.activeElement);
      idx = e.key === 'ArrowDown' ? Math.min(idx + 1, btns.length - 1) : Math.max(idx - 1, 0);
      btns[idx].focus();
    }
    if (e.key === 'Escape') results.style.display = 'none';
  });
  results.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;
    hidden.value = btn.dataset.id;
    input.value = btn.textContent.trim();
    input.classList.add('border-success', 'border-2');
    results.style.display = 'none';
  });
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#' + inputId) && !e.target.closest('#' + resultsId)) {
      results.style.display = 'none';
    }
  });
}
