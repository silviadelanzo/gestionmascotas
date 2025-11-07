<?php require_once __DIR__.'/includes/header.php'; ?>
<?php
/**
 * SERVICIOS - Versión simplificada que usa directamente los datos de la DB
 * Asume que la tabla tiene columnas: tipo, provincia, ciudad normalizadas
 */

// Inicializar variables
$items = [];
$total = 0;
$filters = [
  'q' => trim($_GET['q'] ?? ''),
  'pais' => trim($_GET['pais'] ?? 'Argentina'),
  'provincia' => trim($_GET['provincia'] ?? ''),
  'ciudad' => trim($_GET['ciudad'] ?? ''),
  'tipo' => trim($_GET['tipo'] ?? ''),
];

// Listas desde DB
$provincias = [];
$ciudades = [];
$tipos = [];
$errorMsg = '';

// Conexión a DB
try {
    require_once __DIR__.'/includes/db.php';
    $pdo = db();
    
    // Obtener provincias ÚNICAS desde la DB
    $stmt = $pdo->query("SELECT DISTINCT provincia FROM servicios WHERE provincia IS NOT NULL AND provincia != '' ORDER BY provincia ASC");
    $provincias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Si hay provincia seleccionada, obtener ciudades de esa provincia
    if ($filters['provincia']) {
        $stmt = $pdo->prepare("SELECT DISTINCT ciudad FROM servicios WHERE provincia = :prov AND ciudad IS NOT NULL AND ciudad != '' ORDER BY ciudad ASC");
        $stmt->execute([':prov' => $filters['provincia']]);
        $ciudades = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Obtener tipos ÚNICOS desde la DB
    $stmt = $pdo->query("SELECT DISTINCT tipo FROM servicios WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo ASC");
    $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Construir query de servicios con filtros
    $where = [];
    $params = [];
    
    if ($filters['q']) {
        $where[] = '(nombre LIKE :q OR ciudad LIKE :q OR provincia LIKE :q)';
        $params[':q'] = '%' . $filters['q'] . '%';
    }
    if ($filters['provincia']) {
        $where[] = 'provincia = :provincia';
        $params[':provincia'] = $filters['provincia'];
    }
    if ($filters['ciudad']) {
        $where[] = 'ciudad = :ciudad';
        $params[':ciudad'] = $filters['ciudad'];
    }
    if ($filters['tipo']) {
        $where[] = 'tipo = :tipo';
        $params[':tipo'] = $filters['tipo'];
    }
    
    // Query principal
    $sql = 'SELECT id, nombre, tipo, ciudad, provincia, direccion, latitud, longitud FROM servicios';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY id DESC LIMIT 200';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($items);
    
} catch (Exception $e) {
    $errorMsg = 'Error al cargar servicios: ' . $e->getMessage();
    // Fallback: listas vacías
    $provincias = [];
    $tipos = [];
}

// Si no hay provincias en DB, usar lista estática
if (empty($provincias)) {
    $provincias = [
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
}

// Si no hay tipos en DB, usar lista estática
if (empty($tipos)) {
    $tipos = ['Veterinaria', 'Peluquería', 'Paseo', 'Guardería', 'Adiestramiento', 'Pet Shop', 'Refugio', 'Transporte'];
}
?>

<?php if ($errorMsg): ?>
<div class="alert alert-warning"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<h1 class="h4 mb-3">Servicios por zona <span class="small text-muted" style="font-weight:normal">· v-DB-SIMPLE-3.0 🎯</span></h1>
<form class="row g-2 mb-3" method="get" action="">
  <div class="col-6 col-md-3">
    <select class="form-select" name="pais" id="pais">
      <?php $selAR = ($filters['pais'] === 'Argentina') ? 'selected' : ''; ?>
      <option value="Argentina" <?= $selAR ?>>Argentina</option>
    </select>
  </div>
  <div class="col-6 col-md-3">
    <select class="form-select" name="provincia" id="provincia" onchange="this.form.submit()">
      <option value="">Todas las provincias</option>
      <?php foreach ($provincias as $prov): 
        $sel = ($filters['provincia'] === $prov) ? 'selected' : ''; ?>
        <option value="<?= htmlspecialchars($prov) ?>" <?= $sel ?>><?= htmlspecialchars($prov) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-6 col-md-3">
    <select class="form-select" name="ciudad" id="ciudad" <?= empty($ciudades) ? 'disabled' : '' ?> onchange="this.form.submit()">
      <option value="">Todas las ciudades</option>
      <?php foreach ($ciudades as $c): 
        $sel = ($filters['ciudad'] === $c) ? 'selected' : ''; ?>
        <option value="<?= htmlspecialchars($c) ?>" <?= $sel ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-6 col-md-2">
    <select class="form-select" name="tipo" onchange="this.form.submit()">
      <option value="">Todos los tipos</option>
      <?php foreach ($tipos as $t): 
        $sel = ($filters['tipo'] === $t) ? 'selected' : ''; ?>
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
  // Mapa de Leaflet con los servicios
  const items = <?= json_encode($items, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  let map, group;
  
  function initMap(){
    // Crear mapa centrado en Argentina
    map = L.map('map').setView([-34.6037, -58.3816], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
      maxZoom: 18, 
      attribution: '&copy; OpenStreetMap' 
    }).addTo(map);
    
    // Grupo de marcadores
    group = L.featureGroup().addTo(map);
    
    // Añadir marcadores
    items.forEach(s => {
      const lat = parseFloat(s.latitud);
      const lng = parseFloat(s.longitud);
      if(!isFinite(lat) || !isFinite(lng)) return;
      
      const popup = `
        <div style="min-width:200px;">
          <strong>${s.nombre || 'Servicio'}</strong><br>
          <span style="color:#666;">${s.tipo || ''}</span><br>
          ${s.ciudad || ''}, ${s.provincia || ''}<br>
          ${s.direccion ? '📍 ' + s.direccion : ''}
        </div>
      `;
      
      const marker = L.marker([lat, lng]).bindPopup(popup);
      group.addLayer(marker);
    });
    
    // Ajustar vista a los marcadores
    if(group.getLayers().length){ 
      map.fitBounds(group.getBounds().pad(0.1)); 
    }
  }
  
  function focusMarker(lat, lng, label){
    if(!map) return;
    map.setView([lat, lng], 15);
    // Abrir popup del marcador más cercano
    group.eachLayer(function(layer) {
      const pos = layer.getLatLng();
      if(Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
        layer.openPopup();
      }
    });
  }
  
  // Inicializar mapa cuando carga la página
  document.addEventListener('DOMContentLoaded', initMap);
</script>
<?php require_once __DIR__.'/includes/footer.php'; ?>
