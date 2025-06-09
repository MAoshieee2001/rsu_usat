@extends('adminlte::page')

@section('title', 'Asistencia')

<!--@section('content_header')
@stop-->

@section('content')
<div class="p-2"></div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search"></i> Listado de asistencias</h3>
    </div>
    <div class="card-body">

        <div class="row align-items-end">
            <div class="form-group mr-2">
                <label for="txtFechaInicio"> Fecha Inicio: </label>
                <input id="txtFechaInicio" type="date" class="form-control">
            </div>
            <div class="form-group mr-2">
                <label for="txtFechaFin"> Fecha Final: </label>
                <input id="txtFechaFin" type="date" class="form-control">
            </div>

            <div class="form-group mr-2">
                <label for="txtDniEmpleado"> DNI empleado: </label>
                <input id="txtDniEmpleado" type="text" placeholder="Ingrese DNI." class="form-control">
            </div>

            <div class="form-group">
                <label class="d-block invisible">Buscar</label>
                <button class="btn btn-secondary btn-sm form-control" id="btnBuscar"> <i class="fas fa-search"></i>
                    Buscar</button>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>DNI</th>
                        <th>Empleado</th>
                        <th>Fecha de ingreso</th>
                        <th>Fecha de salida</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i>
            Marcar asistencia
        </button>
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-success"><i class="fas fa-sync"></i>
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
        let select_inicio = $("#txtFechaInicio");
        let select_final = $("#txtFechaFin");
        let txtDni = $("#txtDniEmpleado");
        let table;

        $(document).ready(function () {
            table = $('#tbtEntity').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.attendances.index') }}",
                    data: function (d) {
                        d.fecha_inicio = $('#txtFechaInicio').val();
                        d.fecha_fin = $('#txtFechaFin').val();
                        d.dni = $('#txtDniEmpleado').val();
                    }
                },
                columns: [
                    { data: "dni", width: "10%" },
                    { data: "full_names", width: "20%" },
                    { data: "date_joined", width: "15%" },
                    { data: "date_end", width: "15%" },
                ],
                searching: false,
                lengthChange: false,
                deferLoading: 0 // Indica que no cargue nada al inicio
            });
        })

        $('#btnNuevo').click(function () {
            // Permite aperturar el modal y realizar peticion
            $.ajax({
                url: "{{ route('admin.brands.create') }}",
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-plus'></i> Nueva marca");
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

        $(document).on('click', '.btnEditar', function () {
            var id = $(this).attr("id");
            $.ajax({
                url: "{{ route('admin.brands.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function (response) {
                    $('.modal-title').html("<i class='fas fa-edit'></i> Editar marca");
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

        $(document).on('click', "#btnBuscar", function () {
            if (select_inicio.val() === "" && select_final.val() === "" && txtDni.val() === "") {
                Swal.fire({
                    title: "Ocurrio un error!",
                    icon: "error",
                    timer: 2000,
                    timerProgressBar: true,
                    text: "Debe de seleccionar rango de fechas o buscar por DNI.",
                    confirmButtonText: 'Continuar.',
                });
                return;
            }

            if (select_inicio.val() !== "" && select_final.val() === "") {
                Swal.fire({
                    title: "Ocurrio un error!",
                    icon: "error",
                    timer: 2000,
                    timerProgressBar: true,
                    text: "Debe de seleccionar una fecha fin.",
                    confirmButtonText: 'Continuar.',
                });
                return;
            }


            if (select_inicio.val() === "" && select_final.val() !== "") {
                Swal.fire({
                    title: "Ocurrio un error!",
                    icon: "error",
                    timer: 2000,
                    timerProgressBar: true,
                    text: "Debe de seleccionar una fecha inicio.",
                    confirmButtonText: 'Continuar.',
                });
                return;
            }

            // Recargamos la tabla con esos filtros
            table.ajax.reload();
        });

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