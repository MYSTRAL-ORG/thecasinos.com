<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <script
        src="https://cdn.apple-mapkit.com/mk/5.x.x/mapkit.core.js"
        crossorigin async
        data-callback="initMapKit"
        data-libraries="map"
        data-initial-token="eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IkZDV0hVTDNOQVgifQ.eyJpc3MiOiJWNzhNUzhWUko2IiwiaWF0IjoxNjk2MzYxMTY0LCJleHAiOjE3Mjc5MTM2MDB9.TnIu6y8rDA8PAy33atfAnrceCVaftkD7CZ8JkX8kU5w2h6vgYIi_KkeKss-EMu2EdYRzp7ieQfVopDZsxY7f3A"
    ></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
    <script src='https://unpkg.com/leaflet.mapkitmutant@latest/Leaflet.MapkitMutant.js'></script>

    <style>
        #mapApple {
            width: 100%;
            height: 600px;
        }
    </style>

</head>

<body>
<div id="map" style="width: 100%; height: 500px;"></div>


<script>
    // Remplacez 'YOUR_MAPKIT_API_KEY' par votre clé API MapKit valide
    const apiKey = "eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IkZDV0hVTDNOQVgifQ.eyJpc3MiOiJWNzhNUzhWUko2IiwiaWF0IjoxNjk2MzYxMTY0LCJleHAiOjE3Mjc5MTM2MDB9.TnIu6y8rDA8PAy33atfAnrceCVaftkD7CZ8JkX8kU5w2h6vgYIi_KkeKss-EMu2EdYRzp7ieQfVopDZsxY7f3A";


    let map= null;
    const tokenID = "eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IkZDV0hVTDNOQVgifQ.eyJpc3MiOiJWNzhNUzhWUko2IiwiaWF0IjoxNjk2MzYxMTY0LCJleHAiOjE3Mjc5MTM2MDB9.TnIu6y8rDA8PAy33atfAnrceCVaftkD7CZ8JkX8kU5w2h6vgYIi_KkeKss-EMu2EdYRzp7ieQfVopDZsxY7f3A";
    (async () => {


        if (!window.mapkit || window.mapkit.loadedLibraries.length === 0) {
            // mapkit.core.js or the libraries are not loaded yet.
            // Set up the callback and wait for it to be called.
            await new Promise(resolve => { window.initMapKit = resolve });

            // Clean up
            delete window.initMapKit;



        }


        // Initialisez une carte Leaflet
        const map = L.map('map').setView([37.7749, -122.4194], 10);

        // Créez un layer MapkitMutant et ajoutez-le à la carte
        const mapkitLayer = L.mapkitMutant({
            mapkitKey: apiKey,
            mapOptions: {
                zoomControl: false,
                scaleControl: true,
                scrollWheelZoom: 'center-and-zoom'
            }
        }).addTo(map);

        // Ajoutez un marqueur à la carte
        L.marker([37.7749, -122.4194]).addTo(map)
            .bindPopup('San Francisco, CA')
            .openPopup();

        map.on('zoomout', function() {
            mapkitLayer.source.reload();
        });

    })();



</script>




</body>
</html>
