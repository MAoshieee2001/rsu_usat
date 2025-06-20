<div class="row">

    <div class="form-group col-12">
        {!! Form::label('zone_id', 'Zona') !!}
        {!! Form::select('zone_id', $zones, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione una zona.',
    'required'
]) !!}
    </div>

    <div class="form-group col-12">
        {!! Form::label('name', 'Nombre') !!}
        {!! Form::text('name', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el nombre de la ruta.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

    <div class="form-group col-12">
        {!! Form::label('description', 'Descripción') !!}
        {!! Form::textarea('description', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la descripción de la ruta.',
    'autocomplete' => 'off',
    'rows' => 7,
    'style' => 'resize:none'
]) !!}
    </div>
</div>