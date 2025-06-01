<div class="form-group">
    {!! Form::label('name', 'Nombre del Modelo') !!}
    {!! Form::text('name', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el nombre del tipo vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción del Modelo') !!}
    {!! Form::textarea('description', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la descripción del tipo vehiculo.',
    'style' => 'resize:none',
    'rows' => 4
]) !!}
</div>