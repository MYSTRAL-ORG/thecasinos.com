<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>OpenLayers Google (v3) Layer Example</title>

    <script src="http://maps.google.com/maps/api/js?v=3&key=AIzaSyDc0lLAd3NAvGrE3TIGiceh9UmSZ-ChDJ8"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/openlayers/2.13.1/OpenLayers.js"></script>


    <script>
        var map;

        function init() {
            map = new OpenLayers.Map('map', {
                projection: 'EPSG:3857',
                layers: [
                    new OpenLayers.Layer.Google(
                        "Google Hybrid",
                        {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
                    )
                    ,new OpenLayers.Layer.Google(
                        "Google Physical",
                        {type: google.maps.MapTypeId.TERRAIN}
                    ),
                    new OpenLayers.Layer.Google(
                        "Google Streets", // the default
                        {numZoomLevels: 20}
                    ),

                    new OpenLayers.Layer.Google(
                        "Google Satellite",
                        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
                    )
                ],

                zoom: 4
            });
            map.addControl(new OpenLayers.Control.LayerSwitcher());

            // map.setCenter(new OpenLayers.LonLat(-90.5, 25).transform('EPSG:4326', 'EPSG:3857'),4);

            // add behavior to html
            var animate = document.getElementById("animate");
            animate.onclick = function() {
                for (var i=map.layers.length-1; i>=0; --i) {
                    map.layers[i].animationEnabled = this.checked;
                }
            };
        }
    </script>

</head>
<body onload="init()">
<h1 id="title">Google (v3) Layer Example</h1>
<div id="tags">
    Google, api key, apikey, light
</div>
<p id="shortdesc">
    Demonstrate use the Google Maps v3 API.
</p>
<div id="map" class="smallmap" style="width: 640px;height: 480px;border: 1px solid #000;"></div>
<div id="docs">
    <p><input id="animate" type="checkbox" checked="checked">Animated
        zoom (if supported by GMaps on your device)</input></p>
    <p>
        If you use the Google Maps v3 API with a Google layer, you don't
        need to include an API key.  This layer only works in the
        spherical mercator projection.  See the
        <a href="google-v3.js" target="_blank">google-v3.js source</a>
        to see how this is done.
    </p>
</div>
</body>
</html>
