<?php require_once __DIR__.'/includes/header.php'; ?>
<?php
// Intentar cargar servicios desde DB; si no hay config local, evitar conexión y no mostrar aviso
$items = [];
$total = 0;
$filters = [
  'q' => trim($_GET['q'] ?? ''),
  'pais' => trim($_GET['pais'] ?? 'Argentina'),
  'provincia' => trim($_GET['provincia'] ?? ''),
  'ciudad' => trim($_GET['ciudad'] ?? ''),
  'tipo' => trim($_GET['tipo'] ?? ''),
];
$tipos = [];

// Provincias de Argentina (estático para local)
$provinciasAR = [
  'Buenos Aires',
  'Ciudad Autónoma de Buenos Aires',
  'Catamarca',
  'Chaco',
  'Chubut',
  'Córdoba',
  'Corrientes',
  'Entre Ríos',
  'Formosa',
  'Jujuy',
  'La Pampa',
  'La Rioja',
  'Mendoza',
  'Misiones',
  'Neuquén',
  'Río Negro',
  'Salta',
  'San Juan',
  'San Luis',
  'Santa Cruz',
  'Santa Fe',
  'Santiago del Estero',
  'Tierra del Fuego, Antártida e Islas del Atlántico Sur',
  'Tucumán',
];

$canDb = defined('DB_NAME') && DB_NAME !== '' && defined('DB_USER') && DB_USER !== '';
if ($canDb) {
  try {
      require_once __DIR__.'/includes/db.php';
      $pdo = db();
      // Detectar columna de tipo: preferimos 'rubro'; si no existe, usamos 'tipo'
      $colCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'servicios' AND COLUMN_NAME = :col");
      $colCheck->execute([':col' => 'rubro']);
      $hasRubro = (int)$colCheck->fetchColumn() > 0;
      $colCheck->execute([':col' => 'tipo']);
      $hasTipo = (int)$colCheck->fetchColumn() > 0;

      $tipoExpr = $hasRubro ? 'rubro' : ($hasTipo ? 'tipo' : 'NULL');

      // Tipos para filtro (si existe alguna columna válida)
      if ($tipoExpr !== 'NULL') {
        $tipos = $pdo->query("SELECT DISTINCT $tipoExpr AS tipo FROM servicios WHERE $tipoExpr IS NOT NULL AND $tipoExpr<>'' ORDER BY $tipoExpr ASC")->fetchAll(PDO::FETCH_COLUMN) ?: [];
      }

      $where = [];
      $params = [];
      if ($filters['q'] !== '') { $where[] = '(nombre LIKE :q OR ciudad LIKE :q OR provincia LIKE :q)'; $params[':q'] = '%'.$filters['q'].'%'; }
      if ($filters['provincia'] !== '') { $where[] = 'provincia = :provincia'; $params[':provincia'] = $filters['provincia']; }
      if ($filters['ciudad'] !== '') { $where[] = 'ciudad = :ciudad'; $params[':ciudad'] = $filters['ciudad']; }
      if ($filters['tipo'] !== '') {
        if ($hasRubro) { $where[] = 'rubro = :tipo'; $params[':tipo'] = $filters['tipo']; }
        elseif ($hasTipo) { $where[] = 'tipo = :tipo'; $params[':tipo'] = $filters['tipo']; }
      }

      $sql = 'SELECT id,nombre,' . ($tipoExpr !== 'NULL' ? "$tipoExpr AS tipo" : "NULL AS tipo") . ',ciudad,provincia,direccion,latitud,longitud FROM servicios';
      if ($where) { $sql .= ' WHERE '.implode(' AND ', $where); }
      $sql .= ' ORDER BY id DESC LIMIT 200';
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $items = $stmt->fetchAll();
      $total = count($items);
  } catch (Throwable $e) {
      $items = [];
      $total = 0;
  }
}
// Fallback de tipos si no hay DB
if (!$canDb && !$tipos) {
  $tipos = ['Veterinaria','Peluquería','Paseo','Guardería','Adiestramiento','Pet Shop'];
}
?>
<h1 class="h4 mb-3">Servicios por zona</h1>
<form class="row g-2 mb-3" method="get" action="">
  <div class="col-6 col-md-3">
    <select class="form-select" name="pais" id="pais">
      <?php $selAR = ($filters['pais'] === 'Argentina') ? 'selected' : ''; ?>
      <option value="Argentina" <?= $selAR ?>>Argentina</option>
    </select>
  </div>
  <div class="col-6 col-md-3">
    <select class="form-select" name="provincia" id="provincia">
      <option value="">Provincia</option>
      <?php foreach ($provinciasAR as $prov): $sel = ($filters['provincia'] === $prov) ? 'selected' : ''; ?>
        <option value="<?= htmlspecialchars($prov) ?>" <?= $sel ?>><?= htmlspecialchars($prov) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-6 col-md-3">
    <select class="form-select" name="ciudad" id="ciudad" <?= $filters['provincia'] ? '' : 'disabled' ?>>
      <option value="">Ciudad</option>
    </select>
  </div>
  <div class="col-6 col-md-2">
    <select class="form-select" name="tipo">
      <option value="">Tipo</option>
      <?php foreach ($tipos as $t): $sel = ($filters['tipo']===$t)?'selected':''; ?>
        <option value="<?= htmlspecialchars($t) ?>" <?= $sel ?>><?= htmlspecialchars($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12 col-md-1">
    <button class="btn btn-primary w-100" type="submit">Buscar</button>
  </div>
</form>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div id="map" style="height:360px;border-radius:8px;overflow:hidden;background:#f8f9fa"></div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="small text-muted">Resultados: <?= (int)$total ?></div>
    </div>
    <?php if(!$items): ?>
      <div class="alert alert-info">No encontramos servicios con esos filtros.</div>
    <?php endif; ?>
    <div class="list-group">
      <?php foreach ($items as $s): ?>
        <div class="list-group-item">
          <div class="d-flex justify-content-between">
            <div>
              <div class="fw-semibold"><?= htmlspecialchars($s['nombre']) ?></div>
              <div class="small text-muted">
                <?= htmlspecialchars($s['tipo'] ?: 'Servicio') ?> · <?= htmlspecialchars($s['ciudad'] ?: '-') ?>, <?= htmlspecialchars($s['provincia'] ?: '-') ?>
              </div>
              <?php if(!empty($s['direccion'])): ?>
                <div class="small">📍 <?= htmlspecialchars($s['direccion']) ?></div>
              <?php endif; ?>
            </div>
            <?php if(!empty($s['latitud']) && !empty($s['longitud'])): ?>
              <button class="btn btn-sm btn-outline-primary" onclick="focusMarker(<?= (float)$s['latitud'] ?>, <?= (float)$s['longitud'] ?>, '<?= htmlspecialchars(addslashes($s['nombre'])) ?>')">Ver en mapa</button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const BASE_URL = <?= json_encode(APP_URL) ?>;
  const API_GEO_BASE = 'https://apis.datos.gob.ar/georef/api';
  const preselectedProvincia = <?= json_encode($filters['provincia']) ?>;
  const preselectedCiudad = <?= json_encode($filters['ciudad']) ?>;

  async function getProvincias() {
    // 1) Intentar JSON local opcional (si existiera ar_provincias.json)
    try {
      const res = await fetch(`${BASE_URL}/assets/data/ar_provincias.json`);
      if (res.ok) {
        const data = await res.json();
        if (Array.isArray(data)) return data;
        if (Array.isArray(data.provincias)) return data.provincias.map(p => p.nombre || p);
      }
    } catch(_) {}
    // 2) API oficial
    try {
      const res = await fetch(`${API_GEO_BASE}/provincias?campos=nombre&max=100`);
      if (res.ok) {
        const json = await res.json();
        return (json.provincias||[]).map(p => p.nombre);
      }
    } catch(_) {}
    // 3) Fallback estático
    return [
      'Buenos Aires','Ciudad Autónoma de Buenos Aires','Catamarca','Chaco','Chubut','Córdoba','Corrientes','Entre Ríos','Formosa','Jujuy','La Pampa','La Rioja','Mendoza','Misiones','Neuquén','Río Negro','Salta','San Juan','San Luis','Santa Cruz','Santa Fe','Santiago del Estero','Tierra del Fuego, Antártida e Islas del Atlántico Sur','Tucumán'
    ];
  }

  async function getCiudades(prov) {
    if (!prov) return [];
    // 1) Intentar JSON local completo (si existiera ar_localidades_full.json)
    try {
      const res = await fetch(`${BASE_URL}/assets/data/ar_localidades_full.json`);
      if (res.ok) {
        const data = await res.json();
        const p = (data.provincias||[]).find(x => x.nombre === prov);
        if (p && Array.isArray(p.localidades)) return p.localidades;
      }
    } catch(_) {}
    // 2) API oficial (máximo alto para cubrir la mayoría)
    try {
      const url = `${API_GEO_BASE}/localidades?provincia=${encodeURIComponent(prov)}&campos=nombre&max=5000`;
      const res = await fetch(url);
      if (res.ok) {
        const json = await res.json();
        return (json.localidades||[]).map(c => c.nombre).sort((a,b)=> a.localeCompare(b));
      }
    } catch(_) {}
    // 3) Fallback vacío
    return [];
  }

  async function initUbicacionSelectors(){
    const provSel = document.getElementById('provincia');
    const ciudadSel = document.getElementById('ciudad');

    // Provincias
    const provincias = await getProvincias();
    provSel.innerHTML = '<option value="">Provincia</option>';
    provincias.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p; opt.textContent = p;
      if (p === preselectedProvincia) opt.selected = true;
      provSel.appendChild(opt);
    });

    async function fillCities(pName){
      ciudadSel.innerHTML = '<option value="">Ciudad</option>';
      if (!pName){ ciudadSel.disabled = true; return; }
      const ciudades = await getCiudades(pName);
      ciudades.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        if (c === preselectedCiudad) opt.selected = true;
        ciudadSel.appendChild(opt);
      });
      ciudadSel.disabled = ciudades.length === 0;
    }

    provSel.addEventListener('change', (e)=> fillCities(e.target.value||''));
    if (preselectedProvincia) {
      await fillCities(preselectedProvincia);
    }
  }

  const items = <?= json_encode($items, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  let map, group;
  function initMap(){
    map = L.map('map').setView([-34.6037, -58.3816], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contrib.' }).addTo(map);
    group = L.featureGroup().addTo(map);
    items.forEach(s => {
      const lat = parseFloat(s.latitud), lng = parseFloat(s.longitud);
      if(!isFinite(lat) || !isFinite(lng)) return;
      const m = L.marker([lat, lng]).bindPopup(`<strong>${s.nombre||'Servicio'}</strong><br>${s.tipo||''}<br>${s.ciudad||''}, ${s.provincia||''}`);
      group.addLayer(m);
    });
    if(group.getLayers().length){ map.fitBounds(group.getBounds().pad(0.25)); }
  }
  function focusMarker(lat, lng, label){
    if(!map){ return; }
    map.setView([lat, lng], 15);
  }
  document.addEventListener('DOMContentLoaded', initMap);
  document.addEventListener('DOMContentLoaded', initUbicacionSelectors);
</script>
<?php require_once __DIR__.'/includes/footer.php'; ?>
