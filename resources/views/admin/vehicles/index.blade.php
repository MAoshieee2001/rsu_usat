@extends('adminlte::page')

@section('title', 'Vehiculos')

<!--@section('content_header')
@stop-->

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">

        <h3 class="card-title"><i class="fas fa-search"></i> Lista de Vehiculos</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>Image</th>
                        <th>Code</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Color</th>
                        <th>Placa</th>
                        <th>Año</th>
                        <th>Capacidad</th>
                        <th>Carga</th>
                        <th>Combustible</th>
                        <th>Compatación</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th>Actualización</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    {{--
                    @foreach ($brands as $brand)
                    <tr>
                        <td>
                            <img src="{{ $brand->logo == '' ? asset('storage/brand_logo/no_image.png') : asset($brand->logo) }}"
                                alt="" width="80px" height="50px">
                        </td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->description }}</td>
                        <td>{{ $brand->created_at }}</td>
                        <td>{{ $brand->updated_at }}</td>
                        <td>
                            <button class="btn btn-success btn-sm btnEditar" id="{{ $brand->id }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST"
                                class="frmDelete">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                    @endforeach
                    --}}

                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i>
            Nuevo Registro
        </button>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-success"><i class="fas fa-sync"></i>
            Actualizar
        </a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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

<!-- Modal para ver fotos -->
<div class="modal fade" id="modalFotosVehiculo" tabindex="-1" aria-labelledby="fotosVehiculoLabel" aria-hidden="true"
    role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotosVehiculoLabel">Fotos del Vehículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-wrap gap-3 justify-content-center" id="contenedorImagenes">
                <!-- Aquí se cargan las imágenes por JS -->
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#tbtEntity').DataTable({

                "ajax": "{{ route('admin.vehicles.index') }}",
                "columns": [
                    {
                        "data": "image",
                        "width": "4%",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "code",
                        "width": "4%",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "brand_name",
                    },
                    {
                        "data": "model_name",
                    },

                    {
                        "data": "type_name",
                    },
                    {
                        "data": "name",
                    },
                    {
                        "data": "color_name",
                    },
                    {
                        "data": "plate",
                    },
                    {
                        "data": "year",
                    },
                    {
                        "data": "capacity",
                    },
                    {
                        "data": "load_capacity",
                    },
                    {
                        "data": "fuel_capacity",
                    },
                    {
                        "data": "compaction_capacity",
                    },
                    {
                        "data": "description",
                    },
                    {
                        "data": "status",
                    },
                    {
                        "data": "created_at",
                    },
                    {
                        "data": "updated_at",
                    },
                    {
                        "data": "options",
                        "orderable": false,
                        "searchable": false,
                        "width": "10%",

                    },
                ]
            });
        })

        $('#btnNuevo').click(function () {
            // Permite aperturar el modal y realizar peticion
            $.ajax({
                url: "{{ route('admin.vehicles.create') }}",
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nuevo Vehiculo");
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
                                    draggable: true
                                });
                            },
                            error: function (xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: response.message,
                                    draggable: true
                                });
                            }
                        })
                    })
                }
            })
        })

        $(document).on('click', '.btnEditar', function () {
            var id = $(this).attr("id");
            $.ajax({
                url: "{{ route('admin.vehicles.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-edit'></i> Editar Vehiculo");
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

        $(document).on('click', '.btnFoto', function () {
            const vehicleId = $(this).attr('id');

            $.ajax({
                url: 'vehicles/images/' + vehicleId,
                type: 'GET',
                success: function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(img => {
                            html += `
                            <div class="me-3 mb-3 d-inline-block text-center">
                                <img src="${img.image}" alt="Foto" style="width: 200px; height: 150px; object-fit: contain;" class="img-thumbnail" />
                                <p class="text-center mt-1">${img.profile}</p>
                            </div>
                        `;
                        });
                    } else {
                        html = '<p class="text-center w-100">No hay imágenes para este vehículo.</p>';
                    }

                    $('#contenedorImagenes').html(html);
                    $('#modalFotosVehiculo').modal('show');
                },
                error: function () {
                    alert('Error al cargar imágenes.');
                }
            });
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