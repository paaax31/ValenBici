<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Estaciones Valenbisi</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            text-align: center;
            background-color: #fff5f8;
            color: #333;
        }

        h1 {
            color: #4a2635;
            font-size: 24px;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #map {
            height: 600px;
            width: 90%;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(190, 24, 93, 0.12);
            border: 1px solid #fbcfe8;
        }

        .btn-volver {
            display: inline-block;
            margin-bottom: 20px;
            padding: 12px 24px;
            background-color: #db2777;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(219, 39, 119, 0.2);
            transition: all 0.3s ease;
        }

        .btn-volver:hover {
            background-color: #be185d;
            box-shadow: 0 6px 12px rgba(219, 39, 119, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<h1>Mapeo de Bicicletas en Valencia</h1>

<div id="map"></div>

<div style="margin-top: 15px;">
    <a href="index.php" class="btn-volver">Volver al Listado</a>
</div>

<script>
// 1. Inicializa el mapa centrado en Valencia
var map = L.map('map').setView([39.47, -0.37], 13); 

// 2. Añade la capa base de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

/**
 * Función para determinar el color del marcador según las bicis disponibles 
 */
function getMarkerColor(available) { 
    if (available === 0) { 
        return '#e74c3c'; // Rojo
    } else if (available > 0 && available < 10) { 
        return '#e67e22'; // Naranja
    } else if (available >= 10 && available < 20) { 
        return '#f1c40f'; // Amarillo
    } else { 
        return '#db2777'; // Rosa
    } 
} 

// 3. Cargar el archivo data.json
fetch('data.json') 
    .then(response => { 
        if (!response.ok) { 
            throw new Error(`Error al cargar data.json: ${response.statusText}`); 
        } 
        return response.json(); 
    }) 
    .then(data => { 
        Object.values(data).forEach(station => {
            const { lat, lon, address, available, free, total } = station; 
            
            if (lat && lon) { 
                var colorEstacion = getMarkerColor(available);

                L.circleMarker([lat, lon], {
                    color: colorEstacion,
                    fillColor: colorEstacion,
                    fill: true,
                    radius: 8, 
                    fillOpacity: 0.7,
                    weight: 2
                }) 
                .addTo(map) 
                .bindPopup(` 
                    <strong>${address}</strong><br> 
                    <b>Disponibles:</b> ${available}<br> 
                    <b>Libres:</b> ${free}<br> 
                    <b>Total:</b> ${total} 
                `); 
            } 
        }); 
    }) 
    .catch(error => { 
        console.error('Error cargando los datos:', error); 
    }); 
</script>

</body>
</html>