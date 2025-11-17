<?php
// Mapa de prueba de prestadores con Leaflet + OpenStreetMap
// Solo para demo: los datos están hardcodeados aquí.

$prestadores = [
    [
        'nombre'    => 'Prestador Río Gallegos',
        'direccion' => 'Alberdi 487, Río Gallegos, Santa Cruz',
        'telefono'  => '02966 43-9593',
        'provincia' => 'Santa Cruz',
        'lat'       => -51.6176557,
        'lng'       => -69.2205871,
    ],
    [
        'nombre'    => 'Prestador Santo Tomé',
        'direccion' => 'Candioti 2357, Santo Tomé, Santa Fe',
        'telefono'  => '0342 478-1536',
        'provincia' => 'Santa Fe',
        'lat'       => -31.6707640,
        'lng'       => -60.7550420,
    ],
    [
        'nombre'    => 'Prestador Yerba Buena',
        'direccion' => 'Av. Aconquija 2491, Yerba Buena, Tucumán',
        'telefono'  => '0381 413-2263',
        'provincia' => 'Tucumán',
        'lat'       => -26.8107430,
        'lng'       => -65.3069438,
    ],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa de prestadores - Mascotas y Mimos</title>

    <!-- Leaflet CSS -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""
    />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .page {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }
        header, footer {
            padding: 0.75rem 1rem;
        }
        header h1 {
            margin: 0;
            font-size: 1.1rem;
        }
        #map {
            flex: 1;
            min-height: 320px;
        }
        .leaflet-popup-content h3 {
            margin-top: 0;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }
        .leaflet-popup-content p {
            margin: 0.15rem 0;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="page">
        <header>
            <h1>Mapa de prestadores (demo)</h1>
            <p style="margin:0;font-size:0.85rem;opacity:0.8;">
                Ejemplo con Leaflet + OpenStreetMap usando tres direcciones reales.
            </p>
        </header>

        <div id="map"></div>

        <footer>
            <p style="margin:0;font-size:0.75rem;opacity:0.7;">
                Datos de mapa © OpenStreetMap contributors · Librería de mapas: Leaflet
            </p>
        </footer>
    </div>

    <!-- Leaflet JS -->
    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""
    ></script>

    <script>
        // Centro aproximado de Argentina
        const map = L.map('map').setView([-38.0, -63.0], 4);

        // Capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const prestadores = <?php echo json_encode($prestadores, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const bounds = [];

        prestadores.forEach(p => {
            const marker = L.marker([p.lat, p.lng]).addTo(map);
            marker.bindPopup(
                `<h3>${p.nombre}</h3>
                 <p><strong>Dirección:</strong> ${p.direccion}</p>
                 <p><strong>Provincia:</strong> ${p.provincia}</p>
                 <p><strong>Teléfono:</strong> ${p.telefono}</p>`
            );
            bounds.push([p.lat, p.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [20, 20] });
        }
    </script>
</body>
</html>
