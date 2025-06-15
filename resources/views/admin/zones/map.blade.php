<div id="map_all" style="width: 100%; height: 500px;"></div>

<script>
    window.initMapModal = function () {
        const map = new google.maps.Map(document.getElementById('map_all'), {
            zoom: 14,
            center: { lat: -6.7719, lng: -79.8409 }
        });

        fetch("{{ route('admin.zones.all') }}")
            .then(response => response.json())
            .then(zones => {
                zones.forEach(zone => {
                    const polygon = new google.maps.Polygon({
                        paths: zone.coordinates,
                        strokeColor: zone.color,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: zone.color,
                        fillOpacity: 0.35
                    });

                    polygon.setMap(map);

                    const bounds = new google.maps.LatLngBounds();
                    zone.coordinates.forEach(coord => bounds.extend(coord));
                    map.fitBounds(bounds);
                });
            });
    };
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapModal&loading=async">
    </script>