// Carga dependiente provincia -> localidad (placeholder)
async function cargarProvincias(selectId='provincia') {
  const sel = document.getElementById(selectId);
  if (!sel) return;
  const res = await fetch('/api/provincias_mock.php'); // reemplazar por endpoint real
  const data = await res.json();
  sel.innerHTML = data.items.map(p => '<option value="'+p.id+'">'+p.nombre+'</option>').join('');
}
document.addEventListener('DOMContentLoaded', () => { cargarProvincias(); });