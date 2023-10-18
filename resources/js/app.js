import Map from 'ol/Map.js';
import TileLayer from 'ol/layer/Tile.js';
import View from 'ol/View.js';
import {XYZ} from "ol/source.js";
import VectorSource from "ol/source/Vector.js";
import {GeoJSON} from "ol/format.js";
import {Icon, Stroke, Style} from "ol/style.js";
import VectorLayer from "ol/layer/Vector.js";
import Photo from "ol-ext/style/Photo.js";
import {click} from "ol/events/condition.js";
import {Select} from "ol/interaction.js";
import $ from "jquery";
import Geolocation from 'ol/Geolocation.js';
import {containsCoordinate} from "ol/extent.js";
import element from "ol-ext/util/element.js";





$(document).ready(function () {

    let styleCache = {};

    function generatedStyle(feature, resolution, sel) {
        let img = feature.get("imgurl");
        const originalImg =feature.get("originalimg");

        if(!originalImg){
            img =   sel ?"sel_icon-the-casinos.png"  : "icon-the-casinos.png";

        }

        let style = styleCache[img+sel];

        if (!style ) {
            let imgUrl ='img/casino/'+img;
            let pointer;
            if(!originalImg){
                pointer = new Icon({
                    anchor: [0.5, 150],
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'pixels',
                    src: imgUrl,
                    scale: 0.2,
                    stroke: new Stroke({
                        width: 2,
                        color: sel ? "#ed5c56" : "#fff",
                    }),
                });
            }else{
                pointer = new Photo({
                    transparent: true,
                    src: imgUrl,
                    radius: feature.get("radius"),
                    kind: "circle",
                    crop: true,
                    shadow: 5,
                    onload: function () {
                        casinoVectorLayer6.changed();
                    },
                    displacement: [0, 0],
                    stroke: new Stroke({
                        width: 2,
                        color: sel ? "#ed5c56" : "#fff",
                    }),
                })
            }

            styleCache[img] = style = new Style({
                image: pointer,
            });
        }

        return [style];
    }


    const casinoVectorLayer6 = new VectorLayer({
        source: new VectorSource({
            attributions: ["<img src='/img/icons/google_on_non_white.png'>"]
        }),
        style: generatedStyle,
    });

    const sessionGoogle = $('meta[name="_googleSessionToken"]').attr('content');
    const sessionGoogleKey = $('meta[name="_googleKey"]').attr('content');

    const googleBase = new TileLayer({
        source: new XYZ({
            url: 'https://mt{0-3}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}&scale=2',
            // url:'https://sat-cdn1.apple-mapkit.com/tile?style=7&size=1&scale=1&z=4&x={x}&y={y}&v={z}2&accessKey='+accesKey
            // url: 'https://tile.googleapis.com/v1/2dtiles/{x}/{y}/{z}?style=7&size=1&scale=1&key=AJVsH2zGQIkWpBGEyZa5oSamWrBDNP4_iBKcSkJjjHKYJvJPKnH33qHcOl3uwkrFgCXEXqLfSpym8qrwOscn7nE7VQ',
            // url: "https://tile.googleapis.com/v1/2dtiles/{z}/{x}/{y}/?session=" + sessionGoogle + "&key=" + sessionGoogleKey,


            tilePixelRatio: 2
        })
    });


    let lon = $('meta[name="_lon"]').attr('content');
    let lat = $('meta[name="_lat"]').attr('content');

    let viewClient = null;
    if (lon && lat) {
        viewClient = new View({
            projection: 'EPSG:4326',
            center: [Number(lon), Number(lat)],
            //center: [0,0],
            // -115.1352,36.1450
            zoom: 9,
        });
    } else {
        viewClient = new View({
            projection: 'EPSG:4326',
            center: [0, 0],
            zoom: 0,
        });
    }

    const map = new Map({
        layers: [ googleBase, casinoVectorLayer6],
        target: 'map',
        useInterimTilesOnError: false,
        preload: Infinity,
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

   // const cachedGeoJSON = localStorage.getItem('cachedGeoJSON');
    const appUrl = $('meta[name="_appUrl"]').attr('content');

        // Fetch GeoJSON data from the server
        fetch(appUrl+'/casinos.json')
            .then(function (response) {
                return response.json();
            })
            .then(function (geojson) {
                // Cache the GeoJSON data in local storage
                const geoJsonFormat = new GeoJSON();
                allCasinosFeatures = geoJsonFormat.readFeatures(geojson);
                //localStorage.setItem('cachedGeoJSON', JSON.stringify(geojson));
                updateFeaturesOnExtentChange();
                // Process and display features as needed
            })
            .catch(function (error) {
                console.error('Error fetching GeoJSON:', error);
            });
console.log("dsq");
    async function fetchData() {
        try {
            const response = await fetch(appUrl+'/casinos.json');
            const geojson = await response.json();

            const geoJsonFormat = new GeoJSON();
            allCasinosFeatures = geoJsonFormat.readFeatures(geojson);
            updateFeaturesOnExtentChange();
        } catch (error) {
            console.error('Error:', error);
        }
    }
    fetchData();

    function generatedDivGallery(lstFeatures) {
        const $galleryDiv = $("#gallery");
        $galleryDiv.empty();

        lstFeatures.forEach(function (feature) {
            const imageURL =feature.get("originalimg") ?  '/img/casino/'+feature.get("imgurl") :  '/img/casinos/randomCasinos/'+feature.get("imgurl");
            const location = feature.get('cityname') == undefined ? feature.get('countryname') : feature.get('cityname');
            let $childElement= `
                <div class="emcapsule col-sm-6 col-lg-2">
                    <span class=" block m-2 ">

                        <div class="block-image" style="background-image: url('${imageURL}');">
                            <span data-id-feature="${feature.getId()}"  id="see-more-action-${feature.getId()}"   class="see-more">See more</span>
                        </div>
                        <h3 class="casino-name">${feature.get('name')}</h3>
                        <p class="short-description">Elegance & Golf Course</p>
                        <p class="long-description">Wynn Las Vegas is known for its luxury and has a high-end golf course right on the Strip.</p>
                        <div class="location-info">
                            <img src="/img/icons/location.png" alt="Location Icon" class="location-icon">
                            <span class="city-name">${location}</span>
                            <button class="add-button">see</button>
                         </div>

                    </span>
                </div>
        `;
            $galleryDiv.append($childElement)
        });

        //reload my div gallery to avoid dirty cache

        $(".see-more").click(function () {
            const featureId = $(this).attr("data-id-feature");
            const featureToSelect = casinoVectorLayer6.getSource().getFeatureById(parseInt(featureId));


            clearAllSelectionExcept(featureToSelect);
            if($(this).text() === "See more"){
                select.getFeatures().push(featureToSelect);
            }else{
                select.getFeatures().remove(featureToSelect);
            }
            //jqueryActionGallery($(this));
        });

    }


    function clearAllSelectionExcept(featureToKeep) {
        let selectedFeatures = select.getFeatures();

        selectedFeatures.forEach((feature) => {
            if (feature !== featureToKeep) {
                select.getFeatures().remove(feature);
            }
        });
    }

    map.on('moveend', updateFeaturesOnExtentChange);

    let first6Features;


    function updateFeaturesOnExtentChange() {
        // 1. Get the current extent of the map
        const currentExtent = map.getView().calculateExtent(map.getSize());
        let featuresInExtent = [];
        allCasinosFeatures.forEach(function (feature) {
            const geometry = feature.getGeometry();
            const coordinates = geometry.getCoordinates();
            if (containsCoordinate(currentExtent, coordinates)) {
                featuresInExtent.push(feature);
            }
        });

        featuresInExtent.sort(function (a, b) {
            const propertyA = a.get('squarefootage');
            const propertyB = b.get('squarefootage');
            return propertyB - propertyA; // Modify the comparison as needed
        });

        // 4. Get the first 6 features
        const first12Features = featuresInExtent.slice(0, 12);


        casinoVectorLayer6.getSource().clear();
        let radius = 74;
        first12Features.forEach(function (feature) {

            feature.set('radius', (radius > 40) ? radius : 44);
            radius -= 10;
            casinoVectorLayer6.getSource().addFeature(feature);
        });

        first6Features = first12Features.slice(0, 6);
        generatedDivGallery(first6Features);
    }

    const select = new Select({
        condition: click,
        style: function (feature, resolution) {
            return generatedStyle(feature, resolution, true);
        }
    })
    map.addInteraction(select);


    select.getFeatures().on(['add','remove'], function(e) {
        jqueryActionGallery($("#see-more-action-"+e.element.getId()),e.type);
    });


    //create a function that get randomly string from an array of string and never get the same until  the array
    function getRandomNonRepeatingCasinos(arr) {

        let currentIndex = arr.length;
        let temporaryValue, randomIndex;

        // While there remain elements to shuffle...
        while (currentIndex !== 0) {
            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex--;

            // Swap it with the current element
            temporaryValue = arr[currentIndex];
            arr[currentIndex] = arr[randomIndex];
            arr[randomIndex] = temporaryValue;
        }

        let index = 0;

        if (index >= arr.length) {
            // Handle what to do when all strings have been shown
            return null; // You can return null or take a different action
        }
        return arr[index++];

    }


    function jqueryActionGallery(element , actionType ) {
        const encapsule = element.closest(".emcapsule");
        const allBlock = $(".emcapsule");

        if (actionType === "remove") {
            encapsule.removeClass("expanded  col-lg-4 col-sm-12 ");
            element.text("See more")
            encapsule.addClass("col-sm-6 col-lg-2");
            allBlock.show();
        }else{

            encapsule.addClass(" expanded col-sm-12 col-lg-4");
            allBlock.show();
            element.text("See less");

            if(element.attr("data-id-feature")!== undefined){
                if (encapsule.is(":last-child")) {
                   // Si le bloc est le dernier bloc, cachez le premier bloc
                   $(".emcapsule").first().hide();
                } else {
                   // Sinon, cachez le dernier bloc
                   $(".emcapsule").last().hide();
                }
            }

        }
    }

    function displayNames(featureID) {

        // Use the `find` method to efficiently retrieve the target feature.
        let feature = allCasinosFeatures.find(feature => feature.getId() === featureID);

        if (feature) {  // Check if the feature exists before proceeding.
            const featureGeometry = feature.getGeometry();
            map.getView().fit(featureGeometry, {padding: [20, 20, 20, 20]});
            removeElements();
        } else {
            console.warn(`Feature with ID ${featureID} not found.`);
        }
    }
    let inputElement = document.getElementById('search-casino');
    let resultsList = document.getElementById('autocompleteResults');
    inputElement.addEventListener("keyup", (e) => {//loop through above array
        //Initially remove all elements ( so if user erases a letter or adds new letter then clean previous outputs)
        removeElements();
        //create an  empty array

        if(inputElement.value===""){
            $("#search-casino-list").addClass("d-none");
        }


        let lstElement = [];

        for (let feature of allCasinosFeatures) {
            //convert input to lowercase and compare with each string
            const name = feature.get("name");
            if (
                name.toLowerCase().includes(inputElement.value.toLowerCase()) && inputElement.value !== ""
            ) {
                //create li element
                let listItem = document.createElement("li");
                //One common class name
                listItem.classList.add("list-items");
                listItem.style.cursor = "pointer";

                listItem.addEventListener('click', function() {
                    const view = map.getView();
                    const zoom = view.getZoom();
                    view.animate({
                        center: feature.getGeometry().getCoordinates(),
                        zoom: 8
                    },function() {  // This is the callback after animation completes
                        select.getFeatures().push(feature);
                    });

                    select.getFeatures().clear();
                    $("#search-casino-list").addClass("d-none");
                    removeElements();
                });
                //Display matched part in bold
                // Highlight the matched text

                const imageURL =feature.get("originalimg") ?  '/img/casino/'+feature.get("imgurl") :  '/img/casinos/randomCasinos/'+feature.get("imgurl");
                let word2Display =  '<img class="small-img-search" src="'+imageURL+'" alt="Description of Image">' ;
                const regex = new RegExp(`(${inputElement.value})`, 'gi');
                const name2display = name.replace(regex, "<b>$1</b>");
                const cityOrCountry=  feature.get("cityname") ?? feature.get("countryname") ;
                word2Display+= ' <div class="d-lg-table">                   <div>'+name2display+' </div>                    <div class="text-secondary"> '+cityOrCountry+'  </div>                                    </div>';

                //display the value in array



                listItem.innerHTML = word2Display;
                lstElement.push(word2Display)  ;
                document.querySelector(".search-casino-list").appendChild(listItem);
                if(lstElement.length>=6){
                    $("#search-casino-list").removeClass("d-none");
                    return;
                }
            }
        }
    });



    function removeElements() {
        //clear all the item
        let items = document.querySelectorAll(".list-items");
        items.forEach((item) => {
            item.remove();
        });
    }






    function displayResultsSearch(featureId) {

    }

    window.onresize = function () {
       // fixContentHeight();
    }
    function fixContentHeight() {


        const h = window.innerHeight ;

        const canvasheight = h + 'px';// Modification MM 2022-10-18


        const canvaswidth = $('#map').parent().css('width');
        $('#map').css("height", canvasheight);
        $('#map').css("width", canvaswidth);

        map.updateSize();
    }

   // fixContentHeight();


});


window.addEventListener('beforeunload', function () {
    localStorage.clear(); // Clears all data in localStorage
});
