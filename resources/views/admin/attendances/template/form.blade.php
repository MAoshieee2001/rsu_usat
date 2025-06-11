<div class="row">
    <div class="form-group col-12">
        {!! Form::label('dni', 'DNI del empleado') !!}
        {!! Form::text('dni', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese su DNI.',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>

        <div class="form-group col  -12">
        {!! Form::label('password', 'Contraseña del empleado') !!}
        {!! Form::text('password', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese su contraseña',
    'required',
    'autocomplete' => 'off'
]) !!}
    </div>


</div>