<div class="row">
    <!-- Columna izquierda: datos del empleado -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <!-- DNI -->
                    <div class="form-group col-md-4">
                        {!! Form::label('dni', 'DNI del empleado') !!}
                        {!! Form::text('dni', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el DNI', 'required', 'maxlength' => 8, 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Nombres -->
                    <div class="form-group col-md-4">
                        {!! Form::label('names', 'Nombres del empleado') !!}
                        {!! Form::text('names', null, ['class' => 'form-control', 'placeholder' => 'Ingrese los nombres', 'required', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Apellidos -->
                    <div class="form-group col-md-4">
                        {!! Form::label('lastnames', 'Apellidos') !!}
                        {!! Form::text('lastnames', null, ['class' => 'form-control', 'placeholder' => 'Ingrese los apellidos', 'required', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Fecha nacimiento -->
                    <div class="form-group col-md-4">
                        {!! Form::label('birthday', 'Fecha de nacimiento') !!}
                        {!! Form::date('birthday', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <!-- Licencia -->
                    <div class="form-group col-md-4">
                        {!! Form::label('license', 'Licencia') !!}
                        {!! Form::text('license', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la licencia', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Dirección -->
                    <div class="form-group col-md-4">
                        {!! Form::label('address', 'Dirección') !!}
                        {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la dirección', 'required', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Correo -->
                    <div class="form-group col-md-4">
                        {!! Form::label('email', 'Correo electrónico') !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el correo electrónico', 'required', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group col-md-4">
                        {!! Form::label('phone', 'Teléfono') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el número de teléfono', 'required', 'autocomplete' => 'off']) !!}
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group col-md-4">
                        {!! Form::label('password', 'Contraseña') !!}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Ingrese la contraseña', 'required']) !!}
                    </div>

                    <!-- Estado -->
                    <div class="form-group col-md-4">
                        {!! Form::label('status', 'Estado') !!}
                        {!! Form::select('status', [1 => 'Activo', 0 => 'Inactivo'], null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <!-- Tipo de empleado -->
                    <div class="form-group col-md-4">
                        {!! Form::label('type_id', 'Tipo de empleado') !!}
                        {!! Form::select('type_id', $types, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo', 'required']) !!}
                    </div>

                    <!-- Contrato -->
                    <div class="form-group col-md-4">
                        {!! Form::label('contract_id', 'Tipo de contrato') !!}
                        {!! Form::select('contract_id', $contracts, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un contrato', 'required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna derecha: foto del empleado -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <label for="photoInput" class="d-block font-weight-bold">Foto del empleado</label>
                <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none">

                @php
                    $photoPath = 'storage/employees/default.png';
                    if (isset($employee) && !empty($employee->photo)) {
                        $photoPath = $employee->photo;
                    }
                @endphp

                <div class="border p-2" style="cursor: pointer;" onclick="document.getElementById('photoInput').click();">
                    <img id="photoPreview" src="{{ asset($photoPath) }}" alt="Foto del Empleado"
                        class="img-fluid rounded" style="height: 180px; object-fit: contain;">
                    <p class="text-muted mt-2">Haz clic para seleccionar una imagen</p>
                </div>
            </div>
        </div>
    </div>
</div>