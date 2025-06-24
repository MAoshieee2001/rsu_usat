<div class="row">
    <div class="form-group col-md-6">
        {!! Form::label('zona_id', 'Zona') !!}
        {!! Form::select('zona_id', $zonas, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una zona',
            'required',
            'id' => 'zona_id'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('id_vehicles', 'Vehículo') !!}
        {!! Form::select('id_vehicles', [], null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione un vehículo',
            'required',
            'id' => 'id_vehicles'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('horario_id', 'Turno') !!}
        {!! Form::select('horario_id', [], null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione un turno',
            'required',
            'id' => 'horario_id'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('tipo_id', 'Tipo de Personal') !!}
        {!! Form::select('tipo_id', $types, null, [
            'class' => 'form-control select2',
            'id' => 'tipo_id',
            'placeholder' => 'Seleccione un tipo'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('employee_ids[]', 'Empleados') !!}
        {!! Form::select('employee_ids[]', [], null, [
            'class' => 'form-control select2',
            'id' => 'employee_ids',
            'multiple' => 'multiple',
            'required'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('dias_semana', 'Días de la semana') !!}
        {!! Form::select('dias_semana[]', [
            'LUNES' => 'Lunes',
            'MARTES' => 'Martes',
            'MIERCOLES' => 'Miércoles',
            'JUEVES' => 'Jueves',
            'VIERNES' => 'Viernes',
            'SABADO' => 'Sábado',
            'DOMINGO' => 'Domingo'
        ], null, [
            'class' => 'form-control select2',
            'multiple',
            'required'
        ]) !!}
    </div>

    <div class="form-group col-md-3">
        {!! Form::label('date_joined', 'Fecha de inicio') !!}
        {!! Form::date('date_joined', null, ['class' => 'form-control', 'required']) !!}
    </div>

    <div class="form-group col-md-3">
        {!! Form::label('date_end', 'Fecha de fin') !!}
        {!! Form::date('date_end', null, ['class' => 'form-control', 'required']) !!}
    </div>
</div>

@push('js')
<script>
    $(function () {
        $('.select2').select2({ width: '100%' });

        $('#zona_id').on('change', function () {
            let zonaId = $(this).val();

            if (zonaId) {
                // Cargar vehículos por zona
                $.get(`/admin/zona/${zonaId}/vehiculos`, function (data) {
                    $('#id_vehicles').empty().append('<option value="">Seleccione un vehículo</option>');
                    $.each(data, function (id, name) {
                        $('#id_vehicles').append(new Option(name, id));
                    });
                });

                // Cargar turnos por zona
                $.get(`/admin/zona/${zonaId}/horarios`, function (data) {
                    $('#horario_id').empty().append('<option value="">Seleccione un turno</option>');
                    $.each(data, function (id, name) {
                        $('#horario_id').append(new Option(name, id));
                    });
                });

                // Cargar empleados contratados por zona
                $.get(`/admin/zona/${zonaId}/empleados-contratados`, function (data) {
                    $('#employee_ids').empty();
                    $.each(data, function (id, name) {
                        $('#employee_ids').append(new Option(name, id));
                    });
                });
            }
        });

        $('#tipo_id').on('change', function () {
            let tipoId = $(this).val();
            let zonaId = $('#zona_id').val();

            if (!zonaId) {
                alert("Primero debe seleccionar una zona.");
                return;
            }

            $('#employee_ids').empty().append('<option>Cargando...</option>');

            $.get(`/admin/zona/${zonaId}/empleados-tipo/${tipoId}`, function (data) {
                $('#employee_ids').empty();
                $.each(data, function (id, name) {
                    $('#employee_ids').append(new Option(name, id));
                });
            }).fail(function () {
                $('#employee_ids').html('<option>Error al cargar</option>');
            });
        });
    });
</script>
@endpush