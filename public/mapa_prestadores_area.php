<?php
// Demo: mapa de prestadores mostrando el área (círculo) alrededor de cada uno.

$prestadores = [
    [
        'id'        => 'rg',
        'nombre'    => 'Prestador Río Gallegos',
        'direccion' => 'Alberdi 487, Río Gallegos, Santa Cruz',
        'telefono'  => '02966 43-9593',
        'provincia' => 'Santa Cruz',
        'lat'       => -51.6176557,
        'lng'       => -69.2205871,
    ],
    [
        'id'        => 'st',
        'nombre'    => 'Prestador Santo Tomé',
        'direccion' => 'Candioti 2357, Santo Tomé, Santa Fe',
        'telefono'  => '0342 478-1536',
        'provincia' => 'Santa Fe',
        'lat'       => -31.6707640,
        'lng'       => -60.7550420,
    ],
    [
        'id'        => 'yb',
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
    <title>Mapa de prestadores (área de servicio)</title>

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
        .lista-prestadores {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #ddd;
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
        }
        .prestador-btn {
            flex: 0 0 auto;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            white-space: nowrap;
        }
        .prestador-btn:hover {
            background: #f5f5f5;
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
            <h1>Mapa de prestadores (área)</h1>
            <p style="margin:0;font-size:0.85rem;opacity:0.8;">
                Cada prestador muestra un área de servicio aproximada (círculo). Podés centrar el mapa en cada uno desde la lista.
            </p>
        </header>

        <div class="lista-prestadores">
            <?php foreach ($prestadores as $p): ?>
                <button
                    class="prestador-btn"
                    type="button"
                    data-id="<?php echo htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <?php echo htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="map"></div>

        <footer>
            <p style="margin:0;font-size:0.75rem;opacity:0.7;">
                Datos de mapa © OpenStreetMap contributors · Librería de mapas: Leaflet
            </p>
        </footer>
    </div>

    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""
    ></script>

    <script>
        const prestadores = <?php echo json_encode($prestadores, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const map = L.map('map').setView([-38.0, -63.0], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const markersById = {};
        const bounds = [];

        prestadores.forEach(p => {
            const center = [p.lat, p.lng];
            const marker = L.marker(center).addTo(map);
            marker.bindPopup(
                `<h3>${p.nombre}</h3>
                 <p><strong>Dirección:</strong> ${p.direccion}</p>
                 <p><strong>Provincia:</strong> ${p.provincia}</p>
                 <p><strong>Teléfono:</strong> ${p.telefono}</p>`
            );

            // Área aproximada de servicio (radio en metros, reducido para más detalle en móvil)
            L.circle(center, {
                radius: 1500,
                color: '#ff7f50',
                fillColor: '#ff7f50',
                fillOpacity: 0.15,
                weight: 1,
            }).addTo(map);

            markersById[p.id] = marker;
            bounds.push(center);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [20, 20] });
        }

        function focusPrestador(id) {
            const p = prestadores.find(x => x.id === id);
            const marker = markersById[id];
            if (!p || !marker) return;
            // Zoom más cercano para ver mejor la zona en móvil
            map.setView([p.lat, p.lng], 16);
            marker.openPopup();
        }

        document.querySelectorAll('.prestador-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                focusPrestador(id);
            });
        });
    </script>
</body>
</html>
