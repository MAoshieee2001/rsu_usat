<div class="row">
    <div class="form-group col-12">
        {!! Form::label('name', 'Nombre') !!}
        {!! Form::text('name', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el nombre del horario.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

    <div class="form-group col-12">
        {!! Form::label('description', 'Descripción del horario') !!}
        {!! Form::textarea('description', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la descripción del horairo.',
    'style' => 'resize:none',
    'rows' => 4
]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('date_joined', 'Hora de inicio') !!}
        {!! Form::time('date_joined', null, [
    'class' => 'form-control',
    'required',

]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('date_end', 'Hora de fin') !!}
        {!! Form::time('date_end', null, [
    'class' => 'form-control',
    'required',
]) !!}
    </div>
</div>