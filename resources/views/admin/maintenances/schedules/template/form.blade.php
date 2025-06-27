<div class="row">
    <div class="form-group col-md-6">
        {!! Form::label('vehicle_id', 'Vehículo') !!}
        {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('employee_id', 'Empleado') !!}
        {!! Form::select('employee_id', $employees, null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('day', 'Día') !!}
        {!! Form::select('day', [
            'LUNES' => 'LUNES',
            'MARTES' => 'MARTES',
            'MIÉRCOLES' => 'MIÉRCOLES',
            'JUEVES' => 'JUEVES',
            'VIERNES' => 'VIERNES',
            'SÁBADO' => 'SÁBADO',
        ], null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('type', 'Tipo de mantenimiento') !!}
        {!! Form::select('type', [
            'LIMPIEZA' => 'LIMPIEZA',
            'REPARACIÓN' => 'REPARACIÓN',
        ], null, ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('start_time', 'Hora de inicio') !!}
        {!! Form::time('start_time', null, ['class' => 'form-control', 'required']) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('end_time', 'Hora de fin') !!}
        {!! Form::time('end_time', null, ['class' => 'form-control', 'required']) !!}
    </div>
</div>
