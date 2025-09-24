<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapClone - Inspired by Google Maps</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Internal CSS: Premium Google Maps-inspired design - Clean, blue theme, responsive */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; color: #202124; line-height: 1.4; }
        .header { background: #fff; border-bottom: 1px solid #dadce0; padding: 16px 24px; box-shadow: 0 1px 6px rgba(32,33,36,0.28); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 500; color: #1a73e8; }
        .nav { display: flex; gap: 16px; }
        .nav-btn { background: #1a73e8; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background 0.2s; }
        .nav-btn:hover { background: #1557b0; }
        .main { margin-top: 64px; height: calc(100vh - 64px); position: relative; }
        #map { height: 100%; width: 100%; }
        .sidebar { position: absolute; top: 80px; left: 24px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 16px; width: 300px; max-width: 90vw; z-index: 1000; display: none; }
        .search-container { display: flex; gap: 8px; margin-bottom: 16px; }
        input[type="text"] { flex: 1; padding: 12px; border: 1px solid #dadce0; border-radius: 4px; font-size: 16px; }
        button { background: #1a73e8; color: #fff; border: none; padding: 12px 16px; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #1557b0; }
        .directions-container { display: none; margin-top: 16px; }
        .directions-container label { display: block; margin-bottom: 8px; font-weight: 500; }
        .dir-input { width: 100%; padding: 8px; margin-bottom: 8px; border: 1px solid #dadce0; border-radius: 4px; }
        .info { margin-top: 16px; padding: 8px; background: #e8f0fe; border-radius: 4px; font-size: 14px; }
        .marker-btn { background: #34a853; color: #fff; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-top: 8px; transition: background 0.2s; }
        .marker-btn:hover { background: #2d8e44; }
        .streetview-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center; }
        .streetview-content { background: #fff; padding: 20px; border-radius: 8px; max-width: 90%; max-height: 90%; text-align: center; }
        .close { position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer; color: #fff; }
        /* Responsive */
        @media (max-width: 768px) { .header { padding: 12px; flex-direction: column; gap: 8px; } .sidebar { left: 12px; right: 12px; width: auto; top: 100px; } .nav { width: 100%; justify-content: center; } }
        @media (max-width: 480px) { input[type="text"], .dir-input { font-size: 18px; } } /* Touch-friendly */
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🗺️ MapClone</div>
        <div class="nav">
            <button class="nav-btn" onclick="redirectToFavorites()">Favorites</button>
            <button class="nav-btn" onclick="toggleStreetView()">Street View</button>
        </div>
    </div>
    <div class="main">
        <div id="map"></div>
        <div class="sidebar" id="sidebar">
            <h3>Search Location</h3>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Enter location...">
                <button onclick="searchLocation()">Search</button>
            </div>
            <div class="directions-container" id="directionsContainer">
                <h3>Get Directions</h3>
                <label>Start:</label>
                <input type="text" class="dir-input" id="startInput" placeholder="Starting point">
                <label>End:</label>
                <input type="text" class="dir-input" id="endInput" placeholder="Destination">
                <button onclick="getDirections()">Get Route</button>
            </div>
            <button onclick="toggleDirections()">Toggle Directions</button>
            <div id="info" class="info"></div>
            <button class="marker-btn" onclick="saveCurrentMarker()" id="saveBtn" style="display:none;">Save as Favorite</button>
        </div>
    </div>
    <!-- Street View Modal (Simulated - Optional Feature) -->
    <div class="streetview-modal" id="streetViewModal">
        <span class="close" onclick="closeStreetView()">&times;</span>
        <div class="streetview-content">
            <h2>Street View Simulation</h2>
            <p>Click on map for panoramic view (powered by imagination! Integrate Google API for real).</p>
            <img src="https://via.placeholder.com/400x200/1a73e8/fff?text=Street+View" alt="Street View" style="border-radius:4px;">
        </div>
    </div>
 
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Internal JS: All functionality here
        let map, currentMarker, routeLayer;
        const sidebar = document.getElementById('sidebar');
 
        // Initialize map
        function initMap() {
            map = L.map('map').setView([51.505, -0.09], 13); // Default London view
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            map.on('click', onMapClick);
            sidebar.style.display = 'block'; // Show sidebar
            loadFavorites(); // Load saved pins
        }
 
        // Search location using Nominatim API
        function searchLocation() {
            const query = document.getElementById('searchInput').value;
            if (!query) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const loc = data[0];
                        map.setView([loc.lat, loc.lon], 13);
                        if (currentMarker) map.removeLayer(currentMarker);
                        currentMarker = L.marker([loc.lat, loc.lon]).addTo(map)
                            .bindPopup(`<b>${loc.display_name}</b><br>Lat: ${loc.lat}, Lng: ${loc.lon}`);
                        currentMarker.openPopup();
                        document.getElementById('info').innerHTML = `Found: ${loc.display_name}`;
                        document.getElementById('saveBtn').style.display = 'block';
                    } else {
                        alert('Location not found!');
                    }
                })
                .catch(err => console.error('Search error:', err));
        }
 
        // Get directions using OSRM API
        function getDirections() {
            const start = document.getElementById('startInput').value;
            const end = document.getElementById('endInput').value;
            if (!start || !end) return alert('Enter start and end points!');
            // Geocode start and end (simplified - in pro, chain geocoding)
            Promise.all([
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(start)}`).then(r => r.json()),
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(end)}`).then(r => r.json())
            ]).then(([startData, endData]) => {
                if (startData.length && endData.length) {
                    const coords = `${startData[0].lon},${startData[0].lat};${endData[0].lon},${endData[0].lat}`;
                    fetch(`http://router.project-osrm.org/route/v1/driving/${coords}?overview=full&geometries=geojson`)
                        .then(r => r.json())
                        .then(data => {
                            if (routeLayer) map.removeLayer(routeLayer);
                            routeLayer = L.geoJSON(data.routes[0].geometry).addTo(map);
                            map.fitBounds(routeLayer.getBounds());
                            document.getElementById('info').innerHTML = `Route: ${data.routes[0].distance.toFixed(1)} km, ${Math.round(data.routes[0].duration / 60)} min`;
                        });
                }
            });
        }
 
        // Map click to drop marker
        function onMapClick(e) {
            if (currentMarker) map.removeLayer(currentMarker);
            currentMarker = L.marker(e.latlng).addTo(map)
                .bindPopup(`Lat: ${e.latlng.lat.toFixed(4)}, Lng: ${e.latlng.lng.toFixed(4)}`);
            currentMarker.openPopup();
            document.getElementById('saveBtn').style.display = 'block';
            document.getElementById('info').innerHTML = `Clicked at: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
        }
 
        // Save current marker to DB (AJAX)
        function saveCurrentMarker() {
            if (!currentMarker) return alert('No marker to save!');
            const name = prompt('Location name?') || 'Unnamed';
            fetch('save_location.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `name=${encodeURIComponent(name)}&lat=${currentMarker.getLatLng().lat}&lng=${currentMarker.getLatLng().lng}`
            }).then(res => res.text()).then(data => {
                alert(data);
                loadFavorites(); // Reload pins
            }).catch(err => alert('Save error: ' + err));
        }
 
        // Load favorites from DB
        function loadFavorites() {
            fetch('get_locations.php')
                .then(res => res.json())
                .then(data => {
                    data.forEach(loc => {
                        L.marker([loc.lat, loc.lng]).addTo(map)
                            .bindPopup(`<b>${loc.name}</b><br>${loc.description || ''}`);
                    });
                });
        }
 
        // Toggle directions panel
        function toggleDirections() {
            const cont = document.getElementById('directionsContainer');
            cont.style.display = cont.style.display === 'none' ? 'block' : 'none';
        }
 
        // Street View toggle (optional)
        function toggleStreetView() {
            document.getElementById('streetViewModal').style.display = 'flex';
        }
        function closeStreetView() {
            document.getElementById('streetViewModal').style.display = 'none';
        }
 
        // JS Redirection
        function redirectToFavorites() {
            window.location.href = 'favorites.php';
        }
 
        // Init on load
        window.onload = initMap;
    </script>
</body>
</html>
