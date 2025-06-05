<div class="row">
    <div class="col-12">
        <div class="form-group">
            {!! Form::label('name', 'Nombre del Color') !!}
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'placeholder' => 'Ingrese el nombre del color.',
                'required',
                'autocomplete' => 'off'
            ]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('code', 'Código de Color (RGB Hex)') !!}
            {!! Form::color('code', $color->code ?? '#000000', [
                'class' => 'form-control form-control-color',
                'required'
            ]) !!}
        </div>
    </div>
</div>
