@extends('adminlte::page')

@section('title', 'Rutas de la zona')

<!--@section('content_header')
@stop-->

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">

        <h3 class="card-title"><i class="fas fa-plus"></i> Registro de rutas de la zona</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <div class="card">
                    <div class="card-header">Datos de la zona</div>
                    <div class="card-body">
                        <b>Distrito: {{$route->zone->district->name}}</b> <br>
                        <b>Nombre: {{$route->zone->name}}</b><br>
                        <b>Área: {{$route->zone->area}} m²</b><br>
                        <b>Descripción: {{$route->zone->description}}</b>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        Coordenadas
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tbtEntity">
                                <thead>
                                    <tr>
                                        <th scope="col">Latitud</th>
                                        <th scope="col">Longitud</th>
                                        <th scope="col">Opción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-xs btn-secondary" data-id="{{$route->zone->id}}"
                            id="btnNuevo"><i class="fas fa-plus"></i> Agregar
                            coordenada</button>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">Perimetro</div>
                    <div class="card-body">
                        <div id="map_1" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-save"></i>
            Registrar
        </button>
        <a href="{{ route('admin.routes.index') }}" class="btn btn-warning"><i class="fas fa-minus"></i>
            Retornar
        </a>
        <a href="{{ route('admin.routes.show', $route->id) }}" class="btn btn-success"><i class="fas fa-sync"></i>
            Actualizar
        </a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function refreshTable() {
            var table = $('#tbtEntity').DataTable();
            table.ajax.reload(null, false);
        }

        function updateMap() {
            $.ajax({
                url: "{{ route('admin.routes.getCoords', $route->id) }}",
                type: "GET",
                success: function (data) {
                    const map = new google.maps.Map(document.getElementById('map_1'), {
                        zoom: 18
                    });

                    // Zona (polígono rojo)
                    const polygon = new google.maps.Polygon({
                        paths: data.zone,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.35
                    });
                    polygon.setMap(map);

                    // Ruta (línea azul)
                    const polyline = new google.maps.Polyline({
                        path: data.route,
                        geodesic: true,
                        strokeColor: '#0000FF',
                        strokeOpacity: 1.0,
                        strokeWeight: 3
                    });
                    polyline.setMap(map);

                    // Centrar mapa en base al polígono
                    const bounds = new google.maps.LatLngBounds();
                    data.zone.forEach(coord => bounds.extend(coord));
                    if (data.zone.length > 0) map.panTo(bounds.getCenter());
                }
            });
        }


        $(document).ready(function () {
            $('#tbtEntity').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '/js/es-ES.json'
                },
                "order": [[0, 'asc']],
                "ajax": "{{ route('admin.routes.show', $route->id) }}",
                "columns": [
                    { "data": "latitude" },
                    { "data": "longitude" },
                    {
                        "data": "delete",
                        "width": "4%",
                        "class": "text-center"
                    },
                ]
            });

            $('#btnNuevo').click(function () {
                let route_id = $(this).attr("data-id");
                $.ajax({
                    url: "{{ route('admin.routescoords.edit', '_id') }}".replace("_id", route_id),
                    type: "GET",
                    success: function (response) {
                        $('.modal-title').html("<i class='fas fa-plus'></i> Nuevo perímetro");
                        $('#ModalCenter .modal-body').html(response);
                        $('#ModalCenter').modal('show');

                        $('#ModalCenter form').on('submit', function (e) {
                            e.preventDefault();
                            var form = $(this);
                            var formdata = new FormData(this);

                            $.ajax({
                                url: form.attr('action'),
                                type: form.attr('method'),
                                data: formdata,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    $('#ModalCenter').modal('hide');
                                    refreshTable();
                                    updateMap();
                                    Swal.fire({
                                        title: "Proceso exitoso",
                                        icon: "success",
                                        text: response.message,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: "Error",
                                        icon: "error",
                                        text: xhr.responseJSON.message,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }
                            });
                        });
                    }
                });
            });

            $(document).on('submit', '.frmDelete', function (e) {
                e.preventDefault();
                var form = $(this);
                Swal.fire({
                    title: "¿Está seguro de eliminar?",
                    text: "Este proceso no es reversible.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function (response) {
                                refreshTable();
                                updateMap(); // 🔥 También al eliminar se actualiza el mapa
                                Swal.fire({
                                    title: "Eliminado",
                                    icon: "success",
                                    text: response.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: xhr.responseJSON.message,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    }
                });
            });


        });
        window.initMap = function () {
            const routeCoords = @json($vertice);
            const zoneCoords = @json($zonePolygonCoords);

            const map = new google.maps.Map(document.getElementById('map_1'), {
                zoom: 18
            });

            if (zoneCoords.length > 0) {
                const polygon = new google.maps.Polygon({
                    paths: zoneCoords,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35
                });

                polygon.setMap(map);

                const bounds = new google.maps.LatLngBounds();
                zoneCoords.forEach(coord => bounds.extend(coord));
                map.panTo(bounds.getCenter());
            }

            if (routeCoords.length > 0) {
                const polyline = new google.maps.Polyline({
                    path: routeCoords,
                    geodesic: true,
                    strokeColor: '#0000FF',
                    strokeOpacity: 1.0,
                    strokeWeight: 3
                });

                polyline.setMap(map);
            }
        }


    </script>


    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&loading=async">
        </script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "Proceso exitoso",
                icon: "success",
                text: "{{ session('success') }}",
                timer: 2000,
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "{{ session('error') }}",
                timer: 2000,
                timerProgressBar: true
            });
        </script>
    @endif
@endsection