<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <script
        src="https://cdn.apple-mapkit.com/mk/5.x.x/mapkit.core.js"
        crossorigin async
        data-callback="initMapKit"
        data-libraries="map,annotations,user-location,geojson,overlays"
        data-language="en"
        data-initial-token="eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IkZDV0hVTDNOQVgifQ.eyJpc3MiOiJWNzhNUzhWUko2IiwiaWF0IjoxNjk2MzYxMTY0LCJleHAiOjE3Mjc5MTM2MDB9.TnIu6y8rDA8PAy33atfAnrceCVaftkD7CZ8JkX8kU5w2h6vgYIi_KkeKss-EMu2EdYRzp7ieQfVopDZsxY7f3A"
    ></script>

    <style>
        #mapApple {
            width: 100%;
            height: 600px;
        }
        .bg-black {
            --tw-bg-opacity: 1;
            background-color: rgb(0 0 0/var(--tw-bg-opacity));
        }
        .rounded-full{
            border-radius: 9999px;
        }
        .w-24 {
            width: 6rem;
        }
        .h-24 {
            height: 6rem;
        }
        .border {
            border-width: 1px;
        }
        .border-white {
            --tw-border-opacity: 1;
            border-color: rgb(255 255 255/var(--tw-border-opacity));
        }


    </style>

</head>

<body>
<div id="mapApple"></div>

<script type="module">
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

       mapkit.init({
           authorizationCallback: function(done) {
               done(tokenID);
           }
       });
       function loadGeoJSONData() {
           // Use AJAX to fetch your GeoJSON data
           // Replace 'your_geojson_url' with the URL of your GeoJSON file or service
           fetch('your_geojson_url')
               .then(response => response.json())
               .then(geojsonData => {
                   // Add GeoJSON data to the map
                   map.addOverlay(new mapkit.MarkerCluster(geojsonData.features));
               })
               .catch(error => {
                   console.error('Error loading GeoJSON data:', error);
               });
       }


       // Créer une carte
       const map = new mapkit.Map("mapApple", {
           mapType: "hybrid" // Set the map type to "hybrid"

       });
       map._allowWheelToZoom = true;

       const centerCoordinate =  new mapkit.CoordinateRegion(
           new mapkit.Coordinate(36.1129455, -115.1765067),
           new mapkit.CoordinateSpan(0.05, 0.11));
       // Coordonnées du point à centrer
      // var centerCoordinate = new mapkit.Coordinate(36.1129455, -115.1765067);
       const zommRange = new mapkit.CameraZoomRange(250, 20000)
       // Niveau de zoom initial
       const zoomLevel = 14;

       // Centrer la carte sur le point et ajuster le zoom
     //  map.setCenterAnimated(centerCoordinate,  true);

      // map.cameraZoomRange = zommRange;
       map.cameraBoundary = centerCoordinate;
       map.region = centerCoordinate;
        map.zoom = 8;




       //mapkit.importGeoJSON('casinos.json', geoJSONParserDelegate);
        // Chargez le fichier GeoJSON (ex : en utilisant fetch)
       fetch('casinos.json')
           .then(response => response.json())
           .then(data => {

               data.features.map(feature => {
                   mapkit.importGeoJSON(feature, {
                       styleForOverlay(overlay, geoJSON) {
                           overlay.style.strokeColor = "white";
                           overlay.style.lineDash = [3, 3];

                           return overlay.style;
                       },
                       itemForPoint(coordinate, geoJson){


                           const casinoImageOptions = {
                               title: feature.properties.name,
                               subtitle: feature.properties.address,
                               url: { 1: 'img/casino/'+feature.id+'.jpg'},
                               anchorOffset: new DOMPoint(0, -16),
                               size: {width: 60, height:60},

                               animates : true,
                               displayPriority: 600,
                           };

                           const casinoOptions = {
                               title: feature.properties.name,
                               subtitle: feature.properties.address,

                               animates : true,
                           };

                           if(feature.geometry != null && feature.geometry.coordinates != null){
                               const coordinates =  feature.geometry.coordinates;
                               const coordinateObj = new mapkit.Coordinate(coordinates[1], coordinates[0]);
                               if(feature.properties.imgurl != null ){
                                   return new mapkit.ImageAnnotation(coordinateObj, casinoImageOptions);

                               }else{

                                   return new mapkit.MarkerAnnotation(coordinateObj,casinoOptions);

                               }
                           }
                       },
                       geoJSONDidComplete: function(result, geoJSON) {
                           map.showItems(result.items);

                       },
                       geoJSONDidError: function(error, geoJSON) {

                           console.log('GeoJSONDelegate.geoJSONDidError');
                           console.log(error);
                           console.log(geoJSON);
                       }
                   });





               });





           })
           .catch(error => {
               console.error('Erreur lors du chargement du GeoJSON :', error);
           });
       function createCustomAnnotationElement() {
           var element = document.createElement('div');
           element.className = 'rounded-full w-24 h-24 border border-white overflow-hidden bg-black';

           return element;
       }
    })();


</script>
</body>
</html>
