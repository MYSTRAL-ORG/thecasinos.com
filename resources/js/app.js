import $ from "jquery";


window.addEventListener('load', function () {

    // Fonction debounce pour retarder l'appel de la fonction de recherche
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

// Fonction de recherche qui sera appelée par debounce
    function performSearch(searchQuery) {
        fetch(`/search-casinos?query=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(casinos => {
                removeElements(); // Effacer les résultats précédents
                if (casinos.length > 0) {
                    $("#search-casino-list").removeClass("d-none");
                    casinos.forEach(casino => {
                        const listItem = createListItem(casino, searchQuery);
                        document.querySelector(".search-casino-list").appendChild(listItem);
                    });
                } else {
                    $("#search-casino-list").addClass("d-none");
                }
            })
            .catch(error => console.error('Error fetching search results:', error));
    }

// Fonction pour créer un élément de liste avec les données du casino
    function createListItem(casino, searchQuery) {
        const listItem = document.createElement("li");
        listItem.classList.add("list-items");
        listItem.style.cursor = "pointer";
        listItem.addEventListener('click', function () {
            window.location = `/${casino.country_title}/${casino.city_title}/${casino.slug}`;
        });

        const imageURL = `/img/casino/${casino.img_url}`;
        const nameHighlighted = casino.name.replace(new RegExp(`(${searchQuery})`, 'gi'), "<b>$1</b>");
        const cityOrCountry = casino.city_name || casino.country_name;

        listItem.innerHTML = `<img class="small-img-search" src="${imageURL}" alt="Description of Image">
                          <div class="d-lg-table">
                              <div>${nameHighlighted}</div>
                              <div class="text-secondary">${cityOrCountry}</div>
                          </div>`;
        return listItem;
    }

    function removeElements() {
        document.querySelector(".search-casino-list").innerHTML = '';
    }

    document.getElementById('search-casino').addEventListener("keyup", debounce((e) => {
        const inputElement = e.target;
        const searchQuery = inputElement.value.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        if (searchQuery.length < 3) {
            removeElements(); // Effacer les résultats précédents
            $("#search-casino-list").addClass("d-none");
            return;
        }
        performSearch(searchQuery);
    }, 500)); // Attend 500 ms après que l'utilisateur a fini de taper


    /*

       let inputElement = document.getElementById('search-casino');

       inputElement.addEventListener("keyup", (e) => {//loop through above array
           //Initially remove all elements ( so if user erases a letter or adds new letter then clean previous outputs)
           removeElements();
           //create an  empty array

           if (inputElement.value === "") {
               $("#search-casino-list").addClass("d-none");
           }


           let lstElement = [];

           for (let feature of allCasinosFeatures) {
               //convert input to lowercase and compare with each string
               const name = feature.get("name");
               const nameNormalize = name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
               const searchNormalize = inputElement.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");


               if (
                   nameNormalize.includes(searchNormalize) && inputElement.value !== ""
               ) {
                   //create li element
                   let listItem = document.createElement("li");
                   //One common class name
                   listItem.classList.add("list-items");
                   listItem.style.cursor = "pointer";

                   listItem.addEventListener('click', function () {

                       if (window.location.pathname !== '/') {
                           window.location = '/' + feature.get('country_name') + "/" + feature.get('city_title') + "/" + feature.get('slug');
                       }
                       const view = map.getView();
                       const zoom = view.getZoom();
                       view.animate({
                           center: feature.getGeometry().getCoordinates(),
                           zoom: 18
                       }, function () {  // This is the callback after animation completes
                           select.getFeatures().push(feature);
                       });

                       select.getFeatures().clear();
                       $("#search-casino-list").addClass("d-none");
                       removeElements();
                   });
                   //Display matched part in bold
                   // Highlight the matched text


                   let imageURL = feature.get("originalimg") ? '/img/casino/' + feature.get("img_url") : '/img/casinos/randomCasinos/' + feature.get("img_url");
                   imageURL = appUrl + imageURL;
                   let word2Display = '<img class="small-img-search" src="' + imageURL + '" alt="Description of Image">';
                   const regex = new RegExp(`(${inputElement.value})`, 'gi');
                   const name2display = name.replace(regex, "<b>$1</b>");
                   const cityOrCountry = feature.get("cityname") ?? feature.get("country_name");
                   word2Display += ' <div class="d-lg-table">                   <div>' + name2display + ' </div>                    <div class="text-secondary"> ' + cityOrCountry + '  </div>                                    </div>';

                   //display the value in array


                   listItem.innerHTML = word2Display;
                   lstElement.push(word2Display);
                   document.querySelector(".search-casino-list").appendChild(listItem);
                   if (lstElement.length >= 6) {
                       $("#search-casino-list").removeClass("d-none");
                       return;
                   }
               }
           }
       });

   */
});



