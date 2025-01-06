    $('input.search-box').keydown(function(event) {
        if( event.keyCode == 13 ) {
            event.preventDefault();
            return false;
        }
    });

    var map;
    var marker;
    var center;
    function initMap() {

        center = getPosicion();
        var elem = document.getElementById("map-search");
        if (elem) {
            if ( center == null ) {
                center = {lat: 20.676580, lng: -103.34785};
                if ( navigator.geolocation ) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        center = {lat: pos.coords.latitude, lng: pos.coords.longitude};
                        drawMap(elem, center);
                        //setPosicion(center);
                    }, function () {
                        drawMap(elem, center);
                    });
                } else {
                    drawMap(elem, center);
                }
            } else {
                drawMap(elem, center);
            }
        }
    }

    function drawMap(elem, center) {
        map = new google.maps.Map(elem, {
            center: center,
            zoom: 14
        });
        marker = new google.maps.Marker({
            position: center,
            map: map,
            animation: google.maps.Animation.DROP,
            title: 'Mueve el mapa'
        });
        var options = { componentRestrictions: { /*country: 'mx'*/ } };
        //var searchBox = new google.maps.places.SearchBox(document.getElementById('search-box'), options);
        var searchBox = new google.maps.places.Autocomplete(document.getElementById('search-box'));

        /*Esta parte del código (places_changed) queda obsoleto*/
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();
            var bounds = new google.maps.LatLngBounds();
            var i, place;

            for (i=0; place=places[i]; i++) {
                bounds.extend(place.geometry.location);
                marker.setPosition(place.geometry.location);
            }

            map.fitBounds(bounds);
            map.setZoom(14);
        });
        google.maps.event.addListener(searchBox, 'place_changed', function() {
            
            var place = searchBox.getPlace();

            console.log(place);
            
            // Limpia los inputs de dirección
            $("#calle, #num_ext, #colonia, #codigo_postal, #estado, #municipio").val('');
            
            for (var i = 0; i < place.address_components.length; i++) {
                for (var j = 0; j < place.address_components[i].types.length; j++) {
                    // Calle
                    if ( $.inArray(place.address_components[i].types[j], ["route", "intersection"]) != -1 ) {
                        $("#calle").val(place.address_components[i].long_name);
                    }
                    // Num ext
                    if (place.address_components[i].types[j] == "street_number") {
                        $("#num_ext").val(place.address_components[i].long_name);
                    }
                    // Colonia
                    if ( $.inArray(place.address_components[i].types[j], ["sublocality_level_1", "sublocality"]) != -1 ) {
                        $("#colonia").val(place.address_components[i].long_name);
                    }
                    // Código postal
                    if (place.address_components[i].types[j] == "postal_code") {
                        $("#codigo_postal").val(place.address_components[i].long_name);
                    }
                    // País
                    if ( $.inArray(place.address_components[i].types[j], ["country"]) != -1 ) {
                        $("#pais").val(place.address_components[i].long_name);
                    }
                    // País ISO
                    if ( $.inArray(place.address_components[i].types[j], ["country"]) != -1 ) {
                        $("#pais_iso").val(place.address_components[i].short_name);
                    }
                    // Estado
                    if ( $.inArray(place.address_components[i].types[j], ["administrative_area_level_1"]) != -1 ) {
                        $("#estado").val(place.address_components[i].long_name);
                    }
                    // Municipio
                    if ( $.inArray(place.address_components[i].types[j], ["locality"]) != -1 ) {
                        $("#municipio").val(place.address_components[i].long_name);
                    }
                }
            }

            $("#latitude").val(place.geometry.location.lat());
            $("#longitude").val(place.geometry.location.lng());

            var bounds = new google.maps.LatLngBounds();
            var i, place;
            bounds.extend(place.geometry.location);
            marker.setPosition(place.geometry.location);

            //Move view
            map.fitBounds(bounds);
            map.setZoom(14);
        })
        map.addListener('center_changed', function () {
            var p = map.getCenter();
            marker.setPosition({lat: p.lat(), lng: p.lng()});
            setPosicion({lat: p.lat(), lng: p.lng()});
        });
    }

    function setPosicion(center) {
        $("#latitude").val(center.lat);
        $("#longitude").val(center.lng);
    }

    function getPosicion() {
        if ( $("#latitude").val() != "" ) {
            return {lat: parseFloat($("#latitude").val()), lng: parseFloat($("#longitude").val())};
        }
        return null;
    }
    