@extends('adminlte::page')

@section('title', 'Perimetros')

<!--@section('content_header')
@stop-->

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">

        <h3 class="card-title"><i class="fas fa-plus"></i> Registro de perimetros</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-2">
                <div class="card">
                    <div class="card-header">Datos de la zona</div>
                    <div class="card-body">
                        <b>Distrito: {{$zone->district->name}}</b> <br>
                        <b>Nombre: {{$zone->name}}</b><br>
                        <b>Área: {{$zone->area}}</b><br>
                        <b>Descripción: {{$zone->description}}</b>
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
                        <button type="button" class="btn btn-xs btn-secondary" data-id="{{$zone->id}}" id="btnNuevo"><i
                                class="fas fa-plus"></i> Agregar
                            coordenada</button>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">Perimetro</div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-save"></i>
            Registrar
        </button>
        <a href="{{ route('admin.zones.show', $zone->id) }}" class="btn btn-success"><i class="fas fa-sync"></i>
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
                "ajax": "{{ route('admin.zones.show', $zone->id) }}",
                "columns": [
                    {
                        "data": "latitude",
                    },
                    {
                        "data": "longitude",
                    },
                    {
                        "data": "delete",
                        "width": "4%",
                        "class": "text-center",

                    },
                ]
            });
        })

        $('#btnNuevo').click(function () {
            let zone_id = $(this).attr("data-id");
            // Permite aperturar el modal y realizar peticion
            $.ajax({
                url: "{{ route('admin.zonescoords.edit', '_id') }}".replace("_id", zone_id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nuevo perimetro");
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
        })

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