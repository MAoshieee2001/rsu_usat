<div class="row">

    <div class="form-group col-12">
        {!! Form::label('district_id', 'Distrito') !!}
        {!! Form::select('district_id', $districts, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione un distrito.',
    'required'
]) !!}
    </div>

    <div class="form-group col-12">
        {!! Form::label('name', 'Nombre') !!}
        {!! Form::text('name', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el nombre de la zona.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

    <div class="form-group col-12">
        {!! Form::label('area', 'Área') !!}
        {!! Form::number('area', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el área de la zona.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>
    <div class="form-group col-12">
        {!! Form::label('description', 'Descripción') !!}
        {!! Form::textarea('description', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la descripción de la zona.',
    'autocomplete' => 'off',
    'rows' => 7,
    'style' => 'resize:none'
]) !!}
    </div>

    
    <div class="form-group col-12">
        {!! Form::label('load_requirement', 'Carga de requerimiento') !!}
        {!! Form::text('load_requirement', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la carga de requerimiento.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>
</div>