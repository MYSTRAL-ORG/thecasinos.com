


<style>
    #map {
        height: 500px;
        width: 100%;
        position: relative;
    }
</style>


<div id="map" class="map"></div>

<div id="gallery" class="gallery container-fluid d-flex justify-content-center   @if(Route::currentRouteNamed('casino')) d-none @endif ">



</div>
<meta name="_googleSessionToken" content="{{ $sessionGoogle}}"/>


<meta name="_googleKey" content="{{ config('app.google_key')}}"/>

<meta name="_lon" content="{{ $lon }}"/>
<meta name="_lat" content="{{ $lat }}"/>
<meta name="_fromIndex" content="{{ $fromIndex }}"/>
