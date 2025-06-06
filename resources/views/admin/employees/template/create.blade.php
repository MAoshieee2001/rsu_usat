<div class="row">
    <!-- Columna izquierda: datos del empleado -->
    <div class="col-md-8">
        <div class="row">
            <div class="form-group col-md-4">
                {!! Form::label('dni', 'DNI') !!}
                {!! Form::text('dni', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el DNI',
                    'required',
                    'maxlength' => 8,
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('names', 'Nombres') !!}
                {!! Form::text('names', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese los nombres',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('lastnames', 'Apellidos') !!}
                {!! Form::text('lastnames', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese los apellidos',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('birthday', 'Fecha de nacimiento') !!}
                {!! Form::date('birthday', null, ['class' => 'form-control', 'required']) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('license', 'Licencia') !!}
                {!! Form::text('license', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la licencia',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('address', 'Dirección') !!}
                {!! Form::text('address', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la dirección',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('email', 'Correo electrónico') !!}
                {!! Form::email('email', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el correo electrónico',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('phone', 'Teléfono') !!}
                {!! Form::text('phone', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el número de teléfono',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('password', 'Contraseña') !!}
                {!! Form::password('password', [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la contraseña',
                    'required'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('status', 'Estado') !!}
                {!! Form::select('status', [1 => 'Activo', 0 => 'Inactivo'], null, [
                    'class' => 'form-control',
                    'required'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('type_id', 'Tipo de empleado') !!}
                {!! Form::select('type_id', $types, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione un tipo',
                    'required'
                ]) !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('contract_id', 'Tipo de contrato') !!}
                {!! Form::select('contract_id', $contracts, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione un contrato',
                    'required'
                ]) !!}
            </div>
        </div>
    </div>

    <!-- Columna derecha: foto del empleado -->
    <div class="col-md-4">
        <div class="form-group text-center">
            <label for="photo">Foto:</label>
            <input type="file" id="photoInput" name="photo" accept="image/*" class="form-control-file d-none">
            <div class="p-2 mt-2" style="border: 1px solid #ccc;">
                @php
                    $photoPath = 'storage/employees/default.png';
                    if (isset($employee) && !empty($employee->photo)) {
                        $photoPath = $employee->photo;
                    }
                @endphp
                <img id="photoPreview" src="{{ asset($photoPath) }}" alt="Foto del Empleado"
                    class="img-fluid" style="width: 100%; height: 155px; object-fit: contain; cursor: pointer;">
                <p class="text-center mt-2">Seleccione una foto</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('photoPreview').addEventListener('click', function () {
        document.getElementById('photoInput').click();
    });

    document.getElementById('photoInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file || !(file instanceof Blob)) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>