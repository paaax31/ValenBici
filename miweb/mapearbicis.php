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

        /* Estilos de botones de idioma */
        .lang-container {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .btn-lang {
            background-color: #ffffff;
            border: 2px solid #db2777;
            color: #db2777;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            margin: 0 5px;
            transition: all 0.2s;
        }

        .btn-lang:hover {
            background-color: #db2777;
            color: white;
        }
    </style>
</head>

<body>

<div class="lang-container">
    <button class="btn-lang" onclick="setLang('es')">Español</button>
    <button class="btn-lang" onclick="setLang('en')">English</button>
</div>

<h1 id="main-title">Mapeo de Bicicletas en Valencia</h1>

<div id="map"></div>

<div style="margin-top: 15px;">
    <a href="index.php" id="btn-back" class="btn-volver">Volver al Listado</a>
</div>

<script>
// 1. Inicializa el mapa centrado en Valencia
var map = L.map('map').setView([39.47, -0.37], 13); 

// Variables globales para persistir datos e idioma actual
var estacionesData = [];
var currentLang = 'es';
var markersGroup = L.layerGroup().addTo(map);

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

/**
 * Renderiza los marcadores en el mapa basándose en el idioma actual
 */
function renderMarkers() {
    markersGroup.clearLayers();

    const labelData = {
        'es': { 
            available: "Disponibles", 
            free: "Libres", 
            total: "Total" 
        },
        'en': { 
            available: "Available", 
            free: "Free", 
            total: "Total" 
        }
    };

    estacionesData.forEach(station => {
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
            .bindPopup(` 
                <strong>${address}</strong><br> 
                <b>${labelData[currentLang].available}:</b> ${available}<br> 
                <b>${labelData[currentLang].free}:</b> ${free}<br> 
                <b>${labelData[currentLang].total}:</b> ${total} 
            `)
            .addTo(markersGroup); 
        } 
    });
}

/**
 * Cambia el idioma global de la UI y los Popups del mapa
 */
function setLang(lang) {
    currentLang = lang;

    const uiTranslations = {
        'es': { 
            title: "Mapeo de Bicicletas en Valencia", 
            backBtn: "Volver al Listado" 
        },
        'en': { 
            title: "Valencia Bike Mapping", 
            backBtn: "Back to List" 
        }
    };

    document.getElementById('main-title').innerText = uiTranslations[lang].title;
    document.getElementById('btn-back').innerText = uiTranslations[lang].backBtn;

    if (estacionesData.length > 0) {
        renderMarkers();
    }
}

// 3. Cargar el archivo data.json una sola vez al cargar la web
fetch('data.json') 
    .then(response => { 
        if (!response.ok) { 
            throw new Error(`Error al cargar data.json: ${response.statusText}`); 
        } 
        return response.json(); 
    }) 
    .then(data => { 
        estacionesData = Object.values(data);
        renderMarkers();
    }) 
    .catch(error => { 
        console.error('Error cargando los datos:', error); 
    }); 
</script>

</body>
</html>