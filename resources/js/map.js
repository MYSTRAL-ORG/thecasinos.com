
import Map from 'ol/Map.js';
import OSM from 'ol/source/OSM.js';
import TileLayer from 'ol/layer/Tile.js';
import View from 'ol/View.js';
import {Cluster, TileImage, XYZ} from "ol/source.js";
import VectorImageLayer from "ol/layer/VectorImage.js";
import VectorSource from "ol/source/Vector.js";
import {GeoJSON} from "ol/format.js";
import {Fill, Icon, Stroke, Style} from "ol/style.js";
import VectorLayer from "ol/layer/Vector.js";
import CircleStyle from "ol/style/Circle.js";
import {Circle} from "ol/geom.js";
import {Tile} from "ol";
import BaseLayer from "ol/layer/Base.js";
import Photo from "ol-ext/style/Photo.js";
import {click} from "ol/events/condition.js";
import {Select} from "ol/interaction.js";
import $ from "jquery";
import {forEach} from "ol/geom/flat/segments.js";
import Fixed from "ol-ext/overlay/Fixed.js";
import {createXYZ} from "ol/tilegrid.js";
import TileGrid from "ol/tilegrid/TileGrid.js";
import Geolocation from 'ol/Geolocation.js';
import {containsCoordinate} from "ol/extent.js";
import {Attribution, Control} from "ol/control.js";

$(document).ready(function() {


let styleCache = {};


function generatedStyle(feature,  resolution , sel) {

    let img = feature.get("imgurl");

    let style = styleCache[(img)? img : "icon-the-casinos"];
    if (!style || sel ) {
        let imgUrl;
        if(img){
            imgUrl=   'img/casino/' + feature.getId() + '.jpg';
        }else{
            imgUrl=   'img/icons/icon-the-casinos.png';
        }

        styleCache[img] = style = new  Style ({
            image: new Photo({
                transparent: true,
                src:   imgUrl,
                radius:  feature.get('radius'),
                kind: 'circle',
                crop: true,
                shadow: 5,

                onload: function() { casinoVectorLayer6.changed(); },
                displacement: [0, 0],
                stroke: new Stroke({
                    width: 2,
                    color: sel  ? '#ed5c56' : '#fff'
                })
            })
        });
    }
    return [style];
}






    const casinoVectorLayer6 = new VectorLayer({
        source: new VectorSource(),
        style: generatedStyle,
    });

const sessionGoogle= $('meta[name="_googleSessionToken"]').attr('content');
const sessionGoogleKey= $('meta[name="_googleKey"]').attr('content');

const  googleBase = new TileLayer({
    source: new  XYZ({
       // url: 'https://mt{0-3}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&scale=2&key=AIzaSyDc0lLAd3NAvGrE3TIGiceh9UmSZ-ChDJ8',
      // url:'https://sat-cdn1.apple-mapkit.com/tile?style=7&size=1&scale=1&z=4&x={x}&y={y}&v={z}2&accessKey='+accesKey
       // url: 'https://tile.googleapis.com/v1/2dtiles/{x}/{y}/{z}?style=7&size=1&scale=1&key=AJVsH2zGQIkWpBGEyZa5oSamWrBDNP4_iBKcSkJjjHKYJvJPKnH33qHcOl3uwkrFgCXEXqLfSpym8qrwOscn7nE7VQ',
        url :"https://tile.googleapis.com/v1/2dtiles/{z}/{x}/{y}/?session="+sessionGoogle+"&key="+sessionGoogleKey,
         crossOrigin: 'anonymous',

        tilePixelRatio:2
    })
});


let lon= $('meta[name="_lon"]').attr('content');
let lat= $('meta[name="_lat"]').attr('content');

    let viewClient=null;
if(lon && lat){
   viewClient =  new View({
        projection: 'EPSG:4326',
        center: [ Number(lon),Number(lat) ],
        //center: [0,0],
        // -115.1352,36.1450
        zoom: 9,
    });
}else{
    viewClient = new View({
       projection: 'EPSG:4326',
       center: [0,0],
        zoom: 0,
   });
}

 const map = new Map({
    layers: [googleBase,casinoVectorLayer6],
    target: 'map',
     useInterimTilesOnError: false,
     preload:Infinity,
    view: viewClient,
     controls: []
});

    const geolocation = new Geolocation({
        // enableHighAccuracy must be set to true to have the heading value.
        trackingOptions: {
            enableHighAccuracy: true,
        },

        projection: map.getView().getProjection(),
    });



    geolocation.on('change', function () {
        map.getView().setCenter(geolocation.getPosition());
    });

   // geolocation.setTracking(true);

// Check if GeoJSON data is in local storage

    let allCasinosFeatures = [];

    const cachedGeoJSON = localStorage.getItem('cachedGeoJSON');
    const appUrl = $('meta[name="_appUrl"]').attr('content');
    if (cachedGeoJSON) {
        // Use cached GeoJSON data
        const geoJsonFormat = new  GeoJSON();
        allCasinosFeatures = geoJsonFormat.readFeatures(cachedGeoJSON);
        // Process and display features as needed
    } else {
        // Fetch GeoJSON data from the server
        fetch( '/casinos.json')
            .then(function(response) {
                return response.json();
            })
            .then(function(geojson) {
                // Cache the GeoJSON data in local storage
                const geoJsonFormat = new  GeoJSON();
                allCasinosFeatures = geoJsonFormat.readFeatures(geojson);
                localStorage.setItem('cachedGeoJSON', JSON.stringify(geojson));
                updateFeaturesOnExtentChange();
                // Process and display features as needed
            })
            .catch(function(error) {
                console.error('Error fetching GeoJSON:', error);
            });
    }



    function generatedDivGallery(lstFeatures) {
        const $galleryDiv = $("#gallery");


        $galleryDiv.empty();



        lstFeatures.forEach(function(feature) {

            const imageURL  = '/img/casino/'+feature.getId()+'.jpg';
            const location = feature.get('cityname') == undefined ? feature.get('countryname') : feature.get('cityname');

            const $childElement  = `
            <div class="block">
                <div class="block-image" style="background-image: url('${imageURL}');">
                    <span class="see-more">See more</span>
                </div>
                <h3 class="casino-name">${feature.get('name')}</h3>
                <p class="short-description">Elegance & Golf Course</p>
                <p class="long-description">Wynn Las Vegas is known for its luxury and has a high-end golf course right on the Strip.</p>
                <div class="location-info">
                    <img src="/img/icons/location.png" alt="Location Icon" class="location-icon">
                    <span class="city-name">${location}</span>
                    <button class="add-button">see</button>
                </div>
            </div>
        `;

            // Set the innerHTML of the new div


            // Append the new div to the gallery div
            $galleryDiv.append($childElement)
        });

       //reload my div gallery to avoid dirty cache

        $(".see-more").click(function() {
            const block = $(this).closest(".block");

            // Si le bloc actuel est déjà étendu, fermez-le
            if (block.hasClass("expanded")) {
                block.removeClass("expanded");
                $(this).text("See more");
                // Montrez tous les blocs, y compris celui qui était caché précédemment
                $(".block").show();
                return; // Terminez l'exécution du gestionnaire d'événements
            }

            // Fermer tous les blocs étendus
            $(".block.expanded").removeClass("expanded").find(".see-more").text("See more");

            // Montrez tous les blocs (pour vous assurer que le bloc précédemment caché est à nouveau visible)
            $(".block").show();

            // Étendez le bloc actuellement cliqué
            block.addClass("expanded");
            $(this).text("See less");

            if (block.is(":last-child")) {
                // Si le bloc est le dernier bloc, cachez le premier bloc
                $(".block").first().hide();
            } else {
                // Sinon, cachez le dernier bloc
                $(".block").last().hide();
            }
        });

    }





    map.on('moveend', updateFeaturesOnExtentChange);

    function updateFeaturesOnExtentChange() {
        // 1. Get the current extent of the map
        const currentExtent = map.getView().calculateExtent(map.getSize());
        let featuresInExtent = [];
        allCasinosFeatures.forEach(function(feature) {
            const geometry = feature.getGeometry();
            const coordinates = geometry.getCoordinates();
           if( containsCoordinate(currentExtent, coordinates)){
               featuresInExtent.push(feature);
           }
        });

        featuresInExtent.sort(function(a, b) {
            const propertyA = a.get('squarefootage');
            const propertyB = b.get('squarefootage');
            return propertyB   - propertyA ; // Modify the comparison as needed
        });

        // 4. Get the first 6 features
        const first6Features = featuresInExtent.slice(0, 12);



        casinoVectorLayer6.getSource().clear();
        let radius = 74;
        first6Features.forEach(function(feature) {

            feature.set('radius',  (radius > 40 )? radius : 44);
            radius -= 10;
            casinoVectorLayer6.getSource().addFeature(feature);
        });

        generatedDivGallery(first6Features.slice(0, 6));
    }

const select = new  Select({
    condition:  click,
    multi:false,
    style: function (feature, resolution) {
        return generatedStyle(feature, resolution);
    }
})
map.addInteraction(select);

// onselect



});


window.addEventListener('beforeunload', function() {
    localStorage.clear(); // Clears all data in localStorage
});
