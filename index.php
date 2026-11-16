<?php
// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil data penduduk
$sql = "SELECT * FROM penduduk";
$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Web GIS Data Penduduk</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        body {
            margin: 0;
            background-color: #f8f9fa;
        }

        .navbar {
            height: 56px;
        }

        .navbar-brand {
            font-size: 16px;
            font-weight: bold;
        }

        #map {
            height: 450px;
            width: 100%;
        }

        .container {
            margin-top: 20px;
            margin-bottom: 30px;
        }

        table {
            font-size: 16px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">

            <a class="navbar-brand" href="#">
                WEB GIS DATA PENDUDUK
            </a>

            <a href="input/index.html" class="btn btn-primary btn-sm">
                Input Data
            </a>

        </div>
    </nav>

    <!-- Peta -->
    <div id="map"></div>

    <!-- Tabel Data -->
    <div class="container">

        <h5 class="mb-3">Data Penduduk per Kecamatan</h5>

        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kecamatan</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Luas</th>
                        <th>Jumlah Penduduk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $no = 1;

                    if ($result && $result->num_rows > 0) {

                        foreach ($data as $row) {
                    ?>

                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td><?php echo htmlspecialchars($row['kecamatan']); ?></td>

                            <td><?php echo htmlspecialchars($row['longitude']); ?></td>

                            <td><?php echo htmlspecialchars($row['latitude']); ?></td>

                            <td><?php echo htmlspecialchars($row['luas']); ?></td>

                            <td><?php echo htmlspecialchars($row['jumlah_penduduk']); ?></td>

                            <td>
                                <a href="edit/index.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="delete.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>

                    <?php
                        }

                    } else {
                    ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada data.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>

        // Membuat peta
        var map = L.map('map').setView(
            [-7.677567, 110.406899],
            12
        );

        // Basemap OpenStreetMap
        var osm = L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap'
            }
        ).addTo(map);

        // Basemap Satellite
        var satellite = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            {
                attribution: 'Tiles &copy; Esri'
            }
        );

        // Layer marker kecamatan
        var markerKecamatan = L.layerGroup().addTo(map);

        // Mengambil data langsung dari PHP
        var data = <?php echo json_encode($data); ?>;

        // Menampilkan marker
        var markers = [];

        data.forEach(function(item) {

            var marker = L.marker([
                item.latitude,
                item.longitude
            ]);

            marker.bindPopup(
                "<b>Kecamatan " + item.kecamatan + "</b><br>" +
                "Luas: " + item.luas + " km²<br>" +
                "Jumlah Penduduk: " + item.jumlah_penduduk + " jiwa"
            );

            marker.addTo(markerKecamatan);

            markers.push(marker);

        });

        // Menampilkan seluruh marker
        if (markers.length > 0) {

            var group = L.featureGroup(markers);

            map.fitBounds(group.getBounds());

        }

        // Layer control
        var baseMaps = {
            "OpenStreetMap": osm,
            "Satellite": satellite
        };

        var overlayMaps = {
            "Marker Kecamatan": markerKecamatan
        };

        L.control.layers(baseMaps, overlayMaps).addTo(map);

    </script>

</body>
</html>

<?php
$conn->close();
?>
