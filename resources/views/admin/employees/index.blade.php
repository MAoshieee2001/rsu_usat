@extends('adminlte::page')

@section('title', 'Empleados')

@section('content')
<div class="p-3"></div>

<div class="card shadow-lg">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="fas fa-search"></i> Listado de Empleados</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover text-center w-100" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto</th>
                        <th>DNI</th>
                        <th>Función</th>
                        <th>Nombres Completos</th>
                        <th>Fecha Nacimiento</th>
                        <th>Licencia</th>
                        <th>Dirección</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Contraseña</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Aquí van los datos dinámicos --}}
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i>
            Nuevo Registro
        </button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-success"><i class="fas fa-sync"></i>
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

                "ajax": "{{ route('admin.employees.index') }}",
                "columns": [
                    {
                        "data": "photo",
                        "width": "4%",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "dni",
                    },
                    {
                        "data": "type_name",
                    },
                    {
                        "data": "full_name",
                    },
                    {
                        "data": "birthday",
                    },
                    {
                        "data": "license",
                    },
                    {
                        "data": "address",
                    },
                    {
                        "data": "email",
                    },
                    {
                        "data": "phone",
                    },
                    {
                        "data": "password",
                    },
                    {
                        "data": "status",
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
                url: "{{ route('admin.employees.create') }}",
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nuevo empleado");
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
                url: "{{ route('admin.employees.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-edit'></i> Editar empleado");
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
<style>
    /* Hace que la tabla use el 100% del contenedor */
    .table-responsive {
        overflow-x: auto;
    }

    table#tbtEntity {
        width: 100% !important;
    }

    /* Mejora la apariencia del modal */
    .modal-content {
        border-radius: 0.5rem;
    }

    .card {
        border-radius: 0.5rem;
    }

    .card-header {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .card-footer {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    /* Estilo de botones */
    .btn {
        min-width: 130px;
    }
</style>
@stop