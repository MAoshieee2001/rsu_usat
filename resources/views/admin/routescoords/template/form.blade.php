<input type="text" hidden name="route_id" value="{{ $route->id }}">

<div class="row">
    <div class="form-group col-6">
        {!! Form::label('latitude', 'Latitud') !!}
        {!! Form::text('latitude', optional($lastcoord)->lat, [
    'class' => 'form-control',
    'id' => 'latitude', // 👈 ID correcto para JavaScript
    'placeholder' => 'Ingrese la latitud del perimetro.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('longitude', 'Longitud') !!}
        {!! Form::text('longitude', optional($lastcoord)->lng, [
    'class' => 'form-control',
    'id' => 'longitude', // 👈 ID correcto para JavaScript
    'placeholder' => 'Ingrese la longitud del perimetro.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

    <div class="form-group" id="map" style="width: 100%; height: 400px;">

    </div>
    <script>
        var latInput = document.getElementById('latitude');
        var lonInput = document.getElementById('longitude');

        function initMap() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lonInput.value);

            if (isNaN(lat) || isNaN(lng)) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    latInput.value = lat;
                    lonInput.value = lng;
                    displayMap(lat, lng);
                });
            } else {
                displayMap(lat, lng);
            }
        }

        function displayMap(lat, lng) {
            var mapOptions = {
                center: { lat: lat, lng: lng },
                zoom: 18
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Mostrar el polígono (zona)
            var zonePolygonCoords = @json($zonePolygonCoords);

            var zonePolygon = new google.maps.Polygon({
                paths: zonePolygonCoords,
                strokeColor: '#00AA00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#00AA00',
                fillOpacity: 0.2
            });

            zonePolygon.setMap(map);

            // Mostrar la ruta (Polyline)
            var routeCoords = @json($vertice);

            var routePath = new google.maps.Polyline({
                path: routeCoords,
                geodesic: true,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });

            routePath.setMap(map);

            // Marcador inicial
            var marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                title: 'Ubicación',
                draggable: true
            });

            // Validar si el nuevo punto está dentro del polígono
            google.maps.event.addListener(marker, 'dragend', function (event) {
                var latLng = event.latLng;

                if (google.maps.geometry.poly.containsLocation(latLng, zonePolygon)) {
                    latInput.value = latLng.lat();
                    lonInput.value = latLng.lng();
                } else {
                    alert('El punto está fuera de la zona permitida.');
                    marker.setPosition({ lat: parseFloat(latInput.value), lng: parseFloat(lonInput.value) });
                }
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer>
        </script>