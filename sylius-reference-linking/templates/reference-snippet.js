document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('[data-reference-type][data-sku]');
  if (elements.length === 0) return;
  const type = elements[0].dataset.referenceType;
  const skus = Array.from(elements).map(el => el.dataset.sku);
  fetch('/api/references/resolve-batch', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type, skus })
  }).then(r => r.json()).then(data => {
    for (const el of elements) {
      const url = data.results[el.dataset.sku];
      if (url) {
        el.setAttribute('href', url);
      }
    }
  });
});
