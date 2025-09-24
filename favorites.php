<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - MapClone</title>
    <style>
        /* Internal CSS: Same premium theme as index */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; color: #202124; }
        .header { background: #fff; border-bottom: 1px solid #dadce0; padding: 16px 24px; box-shadow: 0 1px 6px rgba(32,33,36,0.28); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 500; color: #1a73e8; }
        .nav-btn { background: #1a73e8; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; transition: background 0.2s; text-decoration: none; display: inline-block; }
        .nav-btn:hover { background: #1557b0; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 24px; }
        .list { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 24px; }
        .item { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #dadce0; }
        .item:last-child { border-bottom: none; }
        .coords { font-size: 14px; color: #5f6368; }
        .delete-btn { background: #ea4335; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; }
        .delete-btn:hover { background: #d93025; }
        /* Responsive */
        @media (max-width: 768px) { .header { padding: 12px; } .item { flex-direction: column; gap: 8px; text-align: center; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🗺️ MapClone Favorites</div>
        <a href="index.php" class="nav-btn">Back to Map</a>
    </div>
    <div class="container">
        <div class="list" id="favoritesList">
            <!-- Loaded via JS -->
        </div>
    </div>
    <script>
        // Internal JS: Fetch and display favorites
        fetch('get_locations.php')
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('favoritesList');
                if (data.length === 0) {
                    list.innerHTML = '<p>No favorites yet. <a href="index.php">Add some on the map!</a></p>';
                    return;
                }
                data.forEach(loc => {
                    const item = document.createElement('div');
                    item.className = 'item';
                    item.innerHTML = `
                        <div>
                            <strong>${loc.name}</strong><br>
                            <span class="coords">Lat: ${loc.lat}, Lng: ${loc.lng}</span>
                            ${loc.description ? `<br><small>${loc.description}</small>` : ''}
                        </div>
                        <button class="delete-btn" onclick="deleteLocation(${loc.id})">Delete</button>
                    `;
                    list.appendChild(item);
                });
            });
 
        // Delete function (AJAX)
        function deleteLocation(id) {
            if (!confirm('Delete this favorite?')) return;
            fetch('delete_location.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            }).then(res => res.text()).then(msg => {
                alert(msg);
                location.reload(); // Refresh list
            });
        }
    </script>
</body>
</html>
