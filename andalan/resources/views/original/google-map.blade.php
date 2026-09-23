@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Google Maps</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Maps</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Google Maps</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-xl-6 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Default</h6>
                                <div id="default-map" style="height: 400px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Gray Map</h6>
                                <div id="simple-map" style="height: 400px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Map Marker</h6>
                        <div id="map-marker" style="height: 400px"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Directions Service</h6>
                        <div id="floating-panel">
                            <form class="form-inline m-b-20">
                                <div class="form-group">
                                    <label class="col-sm-3 col-form-label">Start</label>
                                    <select class="form-control" id="start">
                                        <option value="chicago, il">Chicago</option>
                                        <option value="st louis, mo">St Louis</option>
                                        <option value="joplin, mo">Joplin, MO</option>
                                        <option value="oklahoma city, ok">Oklahoma City</option>
                                        <option value="amarillo, tx">Amarillo</option>
                                        <option value="gallup, nm">Gallup, NM</option>
                                        <option value="flagstaff, az">Flagstaff, AZ</option>
                                        <option value="winona, az">Winona</option>
                                        <option value="kingman, az">Kingman</option>
                                        <option value="barstow, ca">Barstow</option>
                                        <option value="san bernardino, ca">San Bernardino</option>
                                        <option value="los angeles, ca">Los Angeles</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 col-form-label">End</label>
                                    <select class="form-control" id="end">
                                        <option value="chicago, il">Chicago</option>
                                        <option value="st louis, mo">St Louis</option>
                                        <option value="joplin, mo">Joplin, MO</option>
                                        <option value="oklahoma city, ok">Oklahoma City</option>
                                        <option value="amarillo, tx">Amarillo</option>
                                        <option value="gallup, nm">Gallup, NM</option>
                                        <option value="flagstaff, az">Flagstaff, AZ</option>
                                        <option value="winona, az">Winona</option>
                                        <option value="kingman, az">Kingman</option>
                                        <option value="barstow, ca">Barstow</option>
                                        <option value="san bernardino, ca">San Bernardino</option>
                                        <option value="los angeles, ca">Los Angeles</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div id="directions-simple" style="height: 400px"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>

        var style = [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#bdbdbd"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dadada"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#c9c9c9"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            }
        ];

        function initialize() {
            defaultMap();
            simpleMap();
            mapMarker();
            directionsSimple();
        }

        function defaultMap() {
            var map;
            map = new google.maps.Map(document.getElementById('default-map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 8
            });
        }

        function simpleMap() {
            var map;
            map = new google.maps.Map(document.getElementById('simple-map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 8,
                styles: style
            });
        }

        function mapMarker() {
            var myLatLng = {lat: -25.363, lng: 131.044};

            var map = new google.maps.Map(document.getElementById('map-marker'), {
                zoom: 4,
                center: myLatLng,
                styles: style
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: 'Hello World!'
            });
        }

        function directionsSimple() {
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('directions-simple'), {
                zoom: 7,
                center: {lat: 41.85, lng: -87.65},
                styles: style
            });
            directionsDisplay.setMap(map);

            var onChangeHandler = function () {
                calculateAndDisplayRoute(directionsService, directionsDisplay);
            };
            document.getElementById('start').addEventListener('change', onChangeHandler);
            document.getElementById('end').addEventListener('change', onChangeHandler);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            directionsService.route({
                origin: document.getElementById('start').value,
                destination: document.getElementById('end').value,
                travelMode: 'DRIVING'
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDodpsFh_9UvPq6272RwRWUBqpjxuZ-MfA&callback=initialize"
            async defer></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
