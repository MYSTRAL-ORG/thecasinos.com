
@vite('resources/css/map.css')
@vite('resources/js/map.js')

<style>
    #map {
        height: 500px;
        width: 100%;
    }
</style>
<div id="map" class="map"></div>

<div id="gallery" class="gallery">



</div>
<meta name="_googleSessionToken" content="{{ $sessionGoogle}}"/>

<meta name="_googleKey" content="{{ config('app.google_key')}}"/>
