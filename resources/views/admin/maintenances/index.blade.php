@extends('adminlte::page')

@section('title', 'Programación')

@section('content')
<div class="p-3"></div>

<div class="card shadow-lg">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="fas fa-calendar-alt"></i> Listado de Programaciones</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center" id="tbtEntity">
                <thead class="thead-dark">
                    <tr>
                        <th>Zona</th>
                        <th>Horario</th>
                        <th>Modalidad</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btnNuevo"><i class="fas fa-plus"></i> Nueva Programación</button>
        <a href="{{ route('admin.programming.index') }}" class="btn btn-success"><i class="fas fa-sync"></i> Actualizar</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">...</div>
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
            language: { url: '/js/es-ES.json' },
            ajax: "{{ route('admin.programming.index') }}",
            columns: [
                { data: 'zone_name' },
                { data: 'schedule_name' },
                { data: 'modality_name' },
                { data: 'date_start' },
                { data: 'date_end' },
                {
                    data: 'options',
                    orderable: false,
                    searchable: false,
                    width: "10%"
                },
            ]
        });
    });

    $('#btnNuevo').click(function () {
        $.ajax({
            url: "{{ route('admin.programming.create') }}",
            type: "GET",
            success: function (response) {
                $('.modal-title').html("<i class='fas fa-plus'></i> Nueva Programación");
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
                                text: response.message || "Ha ocurrido un error",
                                timer: 2000,
                                timerProgressBar: true,
                                draggable: true
                            });
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.btnEditar', function () {
        var id = $(this).attr("id");
        $.ajax({
            url: "{{ route('admin.programming.edit', 'id') }}".replace('id', id),
            type: "GET",
            success: function (response) {
                $('.modal-title').html("<i class='fas fa-edit'></i> Editar Programación");
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
                                text: response.message || "Ha ocurrido un error",
                                timer: 2000,
                                timerProgressBar: true,
                                draggable: true
                            });
                        }
                    });
                });
            }
        });
    });

    function refreshTable() {
        var table = $('#tbtEntity').DataTable();
        table.ajax.reload(null, false);
    }

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
            var table = $('#entity').DataTable();
            table.ajax.reload(null, false);
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
</script>
@endsection

@section('css')
{{-- Puedes agregar estilos personalizados aquí si necesitas --}}
@stop
 	