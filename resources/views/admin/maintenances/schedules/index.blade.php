@extends('adminlte::page')

@section('title', 'Horarios de Mantenimiento')

@section('content_header')
    <h1 class="mb-2">Gestión de Horarios de Mantenimiento</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success" id="btnNuevo">
                <i class="fas fa-plus"></i> Nuevo Horario
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableSchedule" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Vehículo</th>
                            <th>Empleado</th>
                            <th>Día</th>
                            <th>Tipo</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modalSchedule" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">Nuevo Horario</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    {{-- Contenido AJAX --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    let table;

    $(document).ready(function () {
        const maintenanceId = @json($maintenance->id);
        console.log("Script cargado. ID de mantenimiento:", maintenanceId);

        // DataTable
        table = $('#tableSchedule').DataTable({
            processing: true,
            serverSide: true,
            ajax: `/admin/maintenances/${maintenanceId}/schedules`, // ← corregido
            columns: [
                { data: 'id' },
                { data: 'vehicle.name' },
                { data: 'employee.name' },
                { data: 'day' },
                { data: 'type' },
                { data: 'start_time' },
                { data: 'end_time' },
                { data: 'options', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        // Botón Nuevo
        $('#btnNuevo').click(function () {
            $('#modalTitle').text('Nuevo Horario');
            $.get(`/admin/maintenances/${maintenanceId}/schedules/create`, function (data) { // ← corregido
                $('#modalContent').html(data);
                $('#modalSchedule').modal('show');
            });
        });

        // Botón Editar
        $(document).on('click', '.btnEditar', function () {
            const id = $(this).attr('id');
            $('#modalTitle').text('Editar Horario');
            $.get(`/admin/maintenances/${maintenanceId}/schedules/${id}/edit`, function (data) { // ← corregido
                $('#modalContent').html(data);
                $('#modalSchedule').modal('show');
            });
        });

        // Submit de formulario AJAX (crear o actualizar)
        $(document).on('submit', 'form.ajaxScheduleForm', function (e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.attr('method');
            let data = form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function (response) {
                    if (response.success) {
                        $('#modalSchedule').modal('hide');
                        $('#modalSchedule').one('hidden.bs.modal', function () {
                            table.ajax.reload(null, false);
                            Swal.fire('¡Éxito!', response.message, 'success');
                            $('#modalContent').html('');
                        });
                    }
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Ocurrió un error';
                    Swal.fire('Error', message, 'error');
                }
            });
        });

        // Eliminar horario
        $(document).on('submit', '.frmDelete', function (e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function (response) {
                            if (response.success) {
                                table.ajax.reload(null, false);
                                Swal.fire('Eliminado', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'No se pudo eliminar el horario.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
