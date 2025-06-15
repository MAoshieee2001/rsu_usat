@extends('adminlte::page')

@section('title', 'Zonas')

<!--@section('content_header')
@stop-->

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">

        <h3 class="card-title"><i class="fas fa-search"></i> Listado de zonas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>Distrito</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Creación</th>
                        <th>Actualización</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i>
            Nuevo Registro
        </button>
        <button type="button" class="btn btn-secondary" id="btnMapaGeneral"><i class="fas fa-map"></i>
            Ver zonas
        </button>
        <a href="{{ route('admin.zones.index') }}" class="btn btn-success"><i class="fas fa-sync"></i>
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
        $(document).ready(function () {
            $('#tbtEntity').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '/js/es-ES.json'
                },
                "ajax": "{{ route('admin.zones.index') }}",
                "columns": [
                    {
                        "data": "district_name",
                        "width": "15%",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "name",
                        "width": "10%",
                    },
                    {
                        "data": "description",
                    },
                    {
                        "data": "area",
                        "render": function (data, type, row) {
                            return data + ' m²';
                        }
                    },
                    {
                        "data": "created_at",
                        "width": "15%",
                    },
                    {
                        "data": "updated_at",
                        "width": "15%",
                    },
                    {
                        "data": "options",
                        "orderable": false,
                        "searchable": false,
                        "width": "10%",

                    },
                ],

            });
        });

        $('#btnNuevo').click(function () {
            // Permite aperturar el modal y realizar peticion
            $.ajax({
                url: "{{ route('admin.zones.create') }}",
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nueva zona");
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
                                Swal.fire({
                                    title: "Proceso exitoso",
                                    icon: "success",
                                    text: response.message,
                                    timer: 2000,
                                    timerProgressBar: true,
                                    draggable: true
                                });
                            },
                            error: function (xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: response.message,
                                    timer: 2000,
                                    timerProgressBar: true,
                                    draggable: true
                                });
                            }
                        })
                    })
                }
            })
        });

        $(document).on('click', '.btnEditar', function () {
            var id = $(this).attr("id");
            $.ajax({
                url: "{{ route('admin.zones.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-edit'></i> Editar zona");
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
                                Swal.fire({
                                    title: "Proceso exitoso",
                                    icon: "success",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    text: response.message,
                                    confirmButtonText: 'Continuar.',
                                    draggable: true
                                });
                            },
                            error: function (xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    text: response.message,
                                    draggable: true
                                });
                            }
                        })
                    })
                }
            })
        });

        $(document).on('submit', '.frmDelete', function (e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "Está seguro de eliminar?",
                text: "Este proceso no es reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "No, Cancelar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    //this.submit();
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function (response) {
                            refreshTable();
                            Swal.fire({
                                title: "Proceso exitoso",
                                icon: "success",
                                timer: 2000,
                                timerProgressBar: true,
                                text: response.message,
                                confirmButtonText: 'Continuar.',
                                draggable: true
                            });
                        },
                        error: function (xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                timer: 2000,
                                timerProgressBar: true,
                                text: response.message,
                                draggable: true
                            });
                        }
                    });
                }
            });
        });

        $('#btnMapaGeneral').click(function () {
            // Crea contenedor para el mapa
            const mapaHTML = '<div id="map_all" style="width:100%; height:500px;"></div>';

            //  Muestra el modal con el mapa vacío
            $('.modal-title').html('<i class="fas fa-map-marked-alt"></i> Mapa general de zonas');
            $('#ModalCenter .modal-body').html(mapaHTML);
            $('#ModalCenter').modal('show');

            // Espera a que el modal esté visible para cargar Google Maps
            $('#ModalCenter').on('shown.bs.modal', function () {
                if (typeof google === 'undefined') {
                    let script = document.createElement('script');
                    script.src = "https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapFromJSON&loading=async";
                    script.async = true;
                    window.initMapFromJSON = drawMapFromJson;
                    document.head.appendChild(script);
                } else {
                    drawMapFromJson(); // Ya está cargado
                }
            });
        });

        // Función para pintar el mapa con zonas desde JSON
        function drawMapFromJson() {
            const map = new google.maps.Map(document.getElementById('map_all'), {
                zoom: 14,
                center: { lat: -6.7719, lng: -79.8409 } // Centro por defecto
            });

            fetch("{{ route('admin.zones.all') }}")
                .then(response => response.json())
                .then(zones => {
                    if (!zones.length) {
                        // No hay zonas: mostrar mensaje o centro default
                        Swal.fire({
                            title: "Sin zonas",
                            icon: "info",
                            text: "No hay zonas registradas para mostrar.",
                            timer: 2000
                        });
                        map.setCenter({ lat: -6.7719, lng: -79.8409 });
                        map.setZoom(14);
                        return;
                    }

                    zones.forEach(zone => {
                        if (!zone.coordinates.length) return; // Evita zonas sin coordenadas

                        const polygon = new google.maps.Polygon({
                            paths: zone.coordinates,
                            strokeColor: zone.color,
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: zone.color,
                            fillOpacity: 0.35
                        });

                        polygon.setMap(map);

                        // Calcular bounds
                        const bounds = new google.maps.LatLngBounds();
                        zone.coordinates.forEach(coord => bounds.extend(coord));

                        if (zone.coordinates.length === 1) {
                            // Solo un punto: setCenter + zoom
                            map.setCenter(zone.coordinates[0]);
                            map.setZoom(18);
                        } else {
                            // Varios puntos: ajustar a bounds
                            map.fitBounds(bounds);
                        }

                        // Opcional: mostrar tooltip con nombre
                        new google.maps.Marker({
                            position: zone.coordinates[0],
                            map: map,
                            label: zone.name,
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 4,
                                fillColor: zone.color,
                                fillOpacity: 1,
                                strokeWeight: 0
                            }
                        });
                    });
                });
        }

        $('#ModalCenter').on('hidden.bs.modal', function () {
            $('#map_all').remove(); // elimina mapa para la próxima carga limpia
        });

        function refreshTable() {
            var table = $('#tbtEntity').DataTable();
            table.ajax.reload(null, false);
        }
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                title: "Proceso exitoso",
                icon: "success",
                timer: 2000,
                timerProgressBar: true,
                text: "{{ session('success') }}",
                draggable: true
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                icon: "error",
                timer: 2000,
                timerProgressBar: true,
                text: "{{ session('error') }}",
                draggable: true
            });
        </script>
    @endif
@endsection

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop