<div class="form-group">
    {!! Form::label('employee_id', 'Empleado') !!}
    {!! Form::select('employee_id', $employee, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione una empleado.',
    'required'
]) !!}
</div>

<div class="form-group col-md-4">
    {!! Form::label('date_start', 'Fecha de inicio') !!}
    {!! Form::date('date_start', null, [
    'class' => 'form-control',
    'required',
    'min' => '1900-01-01',
    'max' => date('Y-m-d'),
]) !!}
</div>