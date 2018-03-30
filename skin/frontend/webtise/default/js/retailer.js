/**
 * Copyright (c) 2017, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @authors daniel (daniel.luo@silksoftware.com)
 * @date    17-3-6
 * @version 0.1.0
 */
var googleRetailer = {
    options: {
        formId: 'near-search-form',
        searchBoxId: 'addressInput',
        containerId: 'leftListContainer',
        markerLabels: 'ABCDEFJHIGKLMNOPQRSTUVWXYZ',
        labelIndex: 0,
        markerIconPath: 'https://developers.google.com/maps/documentation/javascript/images/marker_green',
        radius: 50,
        defaultCoordinate: {lat: 53.797110, lng: -1.543719},
        map: null,
        mapContainerId: 'map',
        markers: [],
        infoWindow: null,
        searchBox: null,
        container: null,
        countryRestrict: {country: 'uk'},
        zoom: 15,
        places: null,
        place: null,
        autocomplete: null,
        mapTypeControl: false,
        panControl: false,
        zoomControl: false,
        streetViewControl: false,
        mapTypeControlOptions: null,
        renderStyle: 'html', // html or form
        autocompleteListener: null,
    },
    init: function()
    {
        this.options.infoWindow = new google.maps.InfoWindow();
        this.options.container = document.getElementById(this.options.containerId);
        return this;
    },
    createMap: function()
    {
        this.options.map = new google.maps.Map(document.getElementById(this.options.mapContainerId), {
            center: this.options.defaultCoordinate,
            zoom: this.options.zoom,
            mapTypeControl: this.options.mapTypeControl,
            panControl: this.options.panControl,
            zoomControl: this.options.zoomControl,
            streetViewControl: this.options.streetViewControl,
            //mapTypeId: 'roadmap'//,
            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
        });
        return this;
    },
    autocompleteAddress: function()
    {
        this.options.searchBox = new google.maps.places.SearchBox(document.getElementById(this.options.searchBoxId));
        google.maps.event.addListener(this.options.searchBox, 'places_changed', function() {
            googleRetailer.options.places = googleRetailer.options.searchBox.getPlaces();
            googleRetailer.options.place = googleRetailer.options.places[0];
            if (!googleRetailer.options.place.geometry) {
                alter('place exist error');
                return;
            }
            googleRetailer.searchLocationsNear(googleRetailer.options.place.geometry.location);
        });
        return this;
        /**
         var center = this.options.defaultCoordinate;
         var input = document.getElementById(this.options.searchBoxId);
         autocomplete = new google.maps.places.Autocomplete(
         input, {
                types: ['(cities)'],
                componentRestrictions: this.options.countryRestrict
            });

         this.options.autocompleteListener = google.maps.event.addListener(autocomplete, 'place_changed', function() {
            if (autocomplete.getPlace().hasOwnProperty('geometry')) {
                center  = autocomplete.getPlace().geometry.location;
                googleRetailer.searchLocationsNear(center)
            } else if(autocomplete.hasOwnProperty('gm_accessors_')) {
                var request = {
                    placeId: autocomplete.gm_accessors_.place.zc.m[0].data[8]
                };

                var service = new google.maps.places.PlacesService(googleRetailer.options.map);

                service.getDetails(request, function(place, status) {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        googleRetailer.searchLocationsNear(place.geometry.location);
                    }
                });

                googleRetailer.autocompleteAddress();
            }
        });

         if (word){
            google.maps.event.addDomListener(window, 'load', function() {
                input.focus();
                input.value = word;
                var oldInput = word;

                if (autocomplete.hasOwnProperty('gm_accessors_')) {
                    var request = {
                        placeId: autocomplete.gm_accessors_.place.zc.m[0].data[8]
                    };

                    var service = new google.maps.places.PlacesService(googleRetailer.options.map);

                    service.getDetails(request, function(place, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            googleRetailer.searchLocationsNear(place.geometry.location);
                            //var marker = new google.maps.Marker({
                            //    map: googleRetailer.options.map,
                            //    position: place.geometry.location
                            //});
                            //google.maps.event.addListener(marker, 'click', function() {
                            //    infowindow.setContent(place.name);
                            //    infowindow.open(googleRetailer.options.map, this);
                            //});
                            //google.maps.event.removeListener(googleRetailer.options.autocompleteListener);
                            //googleRetailer.options.map.fitBounds(place.geometry.viewport);
                        }
                    });

                    input.value = "";
                    google.maps.event.removeListener(googleRetailer.options.autocompleteListener);
                    googleRetailer.autocompleteAddress();
                    input.value = oldInput;
                }

                input.blur();
            });
        } else {
            var marker = new google.maps.Marker({
                map: googleRetailer.options.map,
                position: center
            });
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(center.name);
                infowindow.open(googleRetailer.options.map, this);
            });
            //googleRetailer.options.map.fitBounds(center.geometry.viewport);
        }

         return this;
         //**/
    },
    searchLocationsNear: function(location)
    {
        this.resetMarker();
        var form = $(this.options.formId);
        var searchUrl = form.readAttribute('action') + '?lat=' + location.lat() + '&lng=' + location.lng() + '&radius=' + this.options.radius;
        var request = new Ajax.Request(searchUrl, {
            method: 'post',
            onSuccess: function(response) {
                if (response.status == 200) {
                    var markerNodes = response.responseText.evalJSON();
                    if (googleRetailer.options.renderStyle == 'form') {
                        googleRetailer.renderFormElements(markerNodes);
                    } else {
                        googleRetailer.renderDetailerContainer(location, markerNodes);
                    }
                } else {
                    console.log('Error:');
                    console.log(response);
                }
            },
            onFailure: function(){
                console.log('onFailure');
            },
            parameters: Form.serialize(form)
        });
        return this;
    },
    createMarker: function(latlng, markerData) {
        var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (googleRetailer.options.labelIndex++ % googleRetailer.options.markerLabels.length));
        var markerIcon = googleRetailer.options.markerIconPath + markerLetter + '.png';
        var marker = new google.maps.Marker({
            map: googleRetailer.options.map,
            position: latlng,
            //label: labels[labelIndex++ % labels.length],
            icon: markerIcon
        });

        // add click event to each marker
        google.maps.event.addListener(marker, 'click', function() {
            googleRetailer.options.infoWindow.setContent(googleRetailer.getMarkerHtml(markerData));
            googleRetailer.options.infoWindow.open(googleRetailer.options.map, marker);
        });
        googleRetailer.options.markers.push(marker);
        return this;
    },
    resetMarker: function()
    {
        googleRetailer.options.infoWindow.close();
        for (var i = 0; i < googleRetailer.options.markers.length; i++) {
            googleRetailer.options.markers[i].setMap(null);
            googleRetailer.options.markers[i] = null;
        }
        googleRetailer.options.markers.length = 0;
        googleRetailer.options.labelIndex = 0;
        if (googleRetailer.options.container) googleRetailer.options.container.innerHTML = '';
    },
    markerSelf: function(location)
    {
        var marker = new google.maps.Marker({
            map: this.options.map,
            position: {lat: location.lat(), lng: location.lng()}
        });
        this.options.markers.push(marker);
        return this;
    },
    renderDetailerContainer: function(location, markerNodes) {
        var bounds = new google.maps.LatLngBounds();
        if (markerNodes.length) {
            for (var i = 0; i < markerNodes.length; i++) {
                var latlng = new google.maps.LatLng(
                    parseFloat(markerNodes[i].lat),
                    parseFloat(markerNodes[i].lng)
                );

                googleRetailer.createMarker(latlng, markerNodes[i]);
                googleRetailer.appendToDetailContainer(markerNodes[i], i);
            }
        } else {
            googleRetailer.appendEmptyTipToDetailContainer();
        }

        // marker self
        googleRetailer.markerSelf(location);

        // jump to yourself position
        bounds.extend(location);

        googleRetailer.options.map.fitBounds(bounds);
        // reset map zoom
        googleRetailer.options.map.setZoom(googleRetailer.options.zoom);
        return this;
    },
    appendEmptyTipToDetailContainer: function() {
        var li = document.createElement('li');
        li.insert('<span>There are no shops nearby.</span>');
        this.options.container.appendChild(li);
    },
    appendToDetailContainer: function(markerData, index)
    {
        var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (index % 26));
        var markerIcon = googleRetailer.options.markerIconPath + markerLetter + '.png';
        var li = document.createElement('li');
        li.setAttribute('class', (((index + 1) % 2 == 0) ? 'even' : 'odd') );
        li.setAttribute('style', "list-style-image:url(" + markerIcon + ")");
        li.insert(googleRetailer.getMarkerHtml(markerData));
        li.onclick = function() {
            google.maps.event.trigger(googleRetailer.options.markers[index], 'click');
        };
        googleRetailer.options.container.appendChild(li);
    },
    getMarkerHtml: function(markerData)
    {
        var html  = "<b>Name: " + markerData.name + '</b><br/>';
        html += '<p>Address: ' + markerData.address + '<br/>';
        html += 'Town/City: ' + markerData.town + '<br/>';
        html += 'County/State: ' + markerData.state + '<br/>';
        html += 'Postcode/Zip: ' + markerData.zip + '<br/>';
        html += 'Email: ' + markerData.email + '<br/>';
        html += 'Telephone: ' + markerData.telephone + '<br/>';
        html += 'Distance: ' + markerData.distance + ' mi<br/>';
        html += '</p>';
        return html;
    },
    searchNearByZip: function(zipcode) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({address: zipcode}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                console.log(results[0].geometry.location);
                googleRetailer.searchLocationsNear(results[0].geometry.location);
            } else {
                alert('Can not found postcode:' + zipcode + ', please change other address try again.');
                return;
            }
        });
        return this;
    },
    renderFormElements: function(markerNodes) {
        var html  = '';
        if (markerNodes.length) {
            for (var i = 0; i < markerNodes.length; i++) {
                var label = '';
                var distance = 0;

                if (markerNodes[i].distance == null) {
                    distance = 0;
                } else {
                    distance = markerNodes[i].distance;
                }

                label += markerNodes[i].address + ', ';
                label += markerNodes[i].town + ', ';
                label += markerNodes[i].city + ', ';
                label += markerNodes[i].state + ' ';
                label += 'Distance: ' + distance + 'mi';

                html += '<input title="' + label + '" id="shipping_retailer-'+ i + '" class="shipping_retailer" type="radio" name="shipping_retailer" value="' + markerNodes[i].id + '"/>';
                html += '<label for="shipping_retailer-' + i + '">';
                html += label;
                html += '</label><br/>';
            }
        }
        document.getElementById(googleRetailer.options.containerId).innerHTML = '';
        document.getElementById(googleRetailer.options.containerId).innerHTML = html;
    }
};

// one
//googleRetailer.init().createMap().autocompleteAddress();
// tow
//googleRetailer.options.renderStyle = 'form';
//googleRetailer.options.containerId = 'retailer-list';
//googleRetailer.init().searchNearByZip('SW11');