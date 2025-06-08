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
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Color</th>
                        <th>Placa</th>
                        <th>Capacidad</th>
                        <th>Carga</th>
                        <th>Combustible</th>
                        <th>Compatación</th>
                        <th>Estado</th>
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

<!-- Modal para ver fotos mejorado -->
<div class="modal fade" id="modalFotosVehiculo" tabindex="-1" aria-labelledby="fotosVehiculoLabel" aria-hidden="true"
    role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotosVehiculoLabel">
                    <i class="fas fa-images"></i> Fotos del Vehículo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Instrucciones:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Haz clic en la <strong>estrella</strong> para establecer como foto de perfil</li>
                                <li>Haz clic en el <strong>botón rojo</strong> para eliminar una foto</li>
                                <li>La foto de perfil actual aparece marcada con una estrella dorada</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contenedor de imágenes -->
                <div class="row" id="contenedorImagenes">
                    <!-- Cada imagen se insertará como .col-6 col-md-3 por JS -->
                </div>
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
                        "data": "model_name",
                        "width": "10%",
                    },

                    {
                        "data": "type_name",
                        "width": "10%",
                    },
                    {
                        "data": "name",
                        "width": "10%",
                    },
                    {
                        "data": "color_name",
                        "width": "10%",
                    },
                    {
                        "data": "plate",
                        "width": "7%",
                    },
                    {
                        "data": "capacity",
                        "width": "6%",

                    },
                    {
                        "data": "load_capacity",
                        "width": "6%",

                    },
                    {
                        "data": "fuel_capacity",
                        "width": "6%",

                    },
                    {
                        "data": "compaction_capacity",
                        "width": "6%",
                    },
                    // {
                    //     "data": "description",
                    // },
                    {
                        "data": "status",
                    },
                    // {
                    //     "data": "created_at",
                    // },
                    // {
                    //     "data": "updated_at",
                    // },
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
                                    draggable: true,
                                    timer: 2000,
                                    timerProgressBar: true,
                                });
                            },
                            error: function (xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire({
                                    title: "Error",
                                    icon: "error",
                                    text: response.message,
                                    draggable: true,
                                    timer: 2000,
                                    timerProgressBar: true,
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
                                                                    <img src="${img.image}" alt="Foto" style="width: 150px; height: 70px; object-fit: contain;" class="img-thumbnail" />
                                                                    <p class="text-center mt-1">${img.profile === 1 ? 'Foto de perfil' : 'Foto secundaria'}</p>
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

        $(document).on('click', '.btnFoto', function () {
            const vehicleId = $(this).attr('id');

            $.ajax({
                url: 'vehicles/images/' + vehicleId,
                type: 'GET',
                success: function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(img => {
                            const isProfile = img.is_profile == 1 || img.is_profile == true;
                            const profileBadge = isProfile ?
                                '<span class="badge badge-warning position-absolute" style="top: 5px; left: 5px;"><i class="fas fa-star"></i> Perfil</span>' : '';

                            html += `
                                                                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                                                                        <div class="card shadow-sm">
                                                                            <div class="position-relative">
                                                                                ${profileBadge}
                                                                                <img src="${img.image}" alt="Foto ${img.id}" 
                                                                                    style="width: 100%; height: 250px; object-fit: cover;" 
                                                                                    class="card-img-top" />
                                                                            </div>
                                                                            <div class="card-body p-2">
                                                                                <h6 class="card-title text-center mb-2">${img.profile || 'Sin descripción'}</h6>
                                                                                <div class="text-center">
                                                                                    <div class="btn-group" role="group">
                                                                                        <button type="button" class="btn btn-warning btn-sm btnSetProfile" 
                                                                                                data-image-id="${img.id}" data-vehicle-id="${vehicleId}"
                                                                                                ${isProfile ? 'disabled' : ''}>
                                                                                            <i class="fas fa-star"></i> 
                                                                                            ${isProfile ? 'Es Perfil' : 'Hacer Perfil'}
                                                                                        </button>
                                                                                        <button type="button" class="btn btn-danger btn-sm btnDeleteImage" 
                                                                                                data-image-id="${img.id}" data-vehicle-id="${vehicleId}">
                                                                                            <i class="fas fa-trash"></i> Eliminar
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                `;
                        });
                    } else {
                        html = `
                                                                <div class="col-12">
                                                                    <div class="text-center py-5">
                                                                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                                                        <p class="text-muted">No hay imágenes para este vehículo.</p>
                                                                    </div>
                                                                </div>
                                                            `;
                    }

                    $('#contenedorImagenes').html(html);
                    $('#modalFotosVehiculo').modal('show');
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        icon: "error",
                        text: "Error al cargar las imágenes del vehículo.",
                        draggable: true,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                }
            });
        });

        // Establecer foto como perfil
        $(document).on('click', '.btnSetProfile', function () {
            const imageId = $(this).data('image-id');
            const vehicleId = $(this).data('vehicle-id');

            Swal.fire({
                title: "¿Establecer como foto de perfil?",
                text: "Esta imagen será la que se muestre en la tabla principal.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#ffc107",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Sí, establecer",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `vehicles/images/${imageId}/set-profile`,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            vehicle_id: vehicleId
                        },
                        success: function (response) {
                            // Recargar las imágenes del modal
                            $('.btnFoto[id="' + vehicleId + '"]').click();

                            // Refrescar la tabla principal
                            refreshTable();

                            Swal.fire({
                                title: "¡Perfecto!",
                                icon: "success",
                                text: response.message || "Foto de perfil actualizada correctamente.",
                                timer: 2000,
                                timerProgressBar: true,
                                draggable: true
                            });
                        },
                        error: function (xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: response.message || "Error al establecer la foto de perfil.",
                                draggable: true,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                        }
                    });
                }
            });
        });

        // Eliminar imagen
        $(document).on('click', '.btnDeleteImage', function () {
            const imageId = $(this).data('image-id');
            const vehicleId = $(this).data('vehicle-id');

            Swal.fire({
                title: "¿Eliminar esta imagen?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `vehicles/images/${imageId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            // Recargar las imágenes del modal
                            $('.btnFoto[id="' + vehicleId + '"]').click();

                            // Refrescar la tabla principal
                            refreshTable();

                            Swal.fire({
                                title: "¡Eliminada!",
                                icon: "success",
                                text: response.message || "La imagen ha sido eliminada correctamente.",
                                timer: 2000,
                                timerProgressBar: true,
                                draggable: true
                            });
                        },
                        error: function (xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: response.message || "Error al eliminar la imagen.",
                                draggable: true,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                        }
                    });
                }
            });
        });
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                title: "Proceso exitoso",
                icon: "success",
                text: "{{ session('success') }}",
                draggable: true,
                timer: 2000,
                timerProgressBar: true,
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "{{ session('error') }}",
                draggable: true,
                timer: 2000,
                timerProgressBar: true,
            });
        </script>
    @endif
@endsection

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
<style>
    #modalFotosVehiculo .card {
        transition: transform 0.2s ease-in-out;
        margin-bottom: 1rem;
    }

    #modalFotosVehiculo .card:hover {
        transform: translateY(-2px);
    }

    #modalFotosVehiculo .btn-group {
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    #modalFotosVehiculo .btn-group .btn {
        font-size: 0.5rem;
        padding: 0.25rem 0.5rem;
        margin-top: 0.25rem;
    }

    #modalFotosVehiculo .card-img-top {
        border-bottom: 1px solid #dee2e6;
        max-height: 140px;
        object-fit: cover;
        width: 50%;
    }

    @media (max-width: 400px) {
        #modalFotosVehiculo .btn-group {
            flex-direction: column;
            gap: 2px;
        }

        #modalFotosVehiculo .btn-group .btn {
            width: 50%;
        }
    }
</style>

@stop