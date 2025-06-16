<div class="row">
    <!-- Columna izquierda: Datos del empleado -->
    <div class="col-lg-8 mb-4">
        <div class="row">
            @php
                $fields = [
                    ['dni', 'DNI', 'text', 'Ingrese el DNI'],
                    ['birthday', 'Fecha de Nacimiento', 'date', ''],
                    ['names', 'Nombres', 'text', 'Ingrese nombres'],
                    ['lastnames', 'Apellidos', 'text', 'Ingrese apellidos'],
                    ['address', 'Dirección', 'text', 'Ingrese la dirección'],
                    ['email', 'Correo Electrónico', 'email', 'Ingrese el correo'],
                    ['phone', 'Teléfono', 'text', 'Ingrese el teléfono'],
                ];
            @endphp

            @foreach ($fields as [$name, $label, $type, $placeholder])
                <div class="form-group col-md-6">
                    {!! Form::label($name, $label) !!}
                    @if($name === 'birthday')
                        {!! Form::$type($name, null, [
                            'class' => 'form-control',
                            'placeholder' => $placeholder,
                            'required',
                            'autocomplete' => 'off',
                            'max' => date('Y-m-d', strtotime('-18 years')),
                            'id' => 'birthday'
                        ]) !!}
                        <small class="text-muted">Debe ser mayor de 18 años</small>
                    @else
                        {!! Form::$type($name, null, [
                            'class' => 'form-control',
                            'placeholder' => $placeholder,
                            'required',
                            'autocomplete' => 'off'
                        ]) !!}
                    @endif
                </div>
            @endforeach

            <!-- Estado-->
            <div class="form-group col-md-6">
                {!! Form::label('status', 'Estado') !!}
                {!! Form::select('status', [1 => 'Activo', 0 => 'Inactivo'], $employees->status ?? 1, [
                    'class' => 'form-control'
                ]) !!}
            </div>

            <!-- Contraseña -->
            <div class="form-group col-md-6">
                {!! Form::label('password', 'Contraseña') !!}
                {!! Form::password('password', [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la contraseña',
                    'required' => !isset($employees),
                    'autocomplete' => 'new-password',
                    'id' => 'password'
                ]) !!}
            </div>

            <!-- Confirmación de Contraseña -->
            <div class="form-group col-md-6">
                {!! Form::label('password_confirmation', 'Confirmar Contraseña') !!}
                {!! Form::password('password_confirmation', [
                    'class' => 'form-control',
                    'placeholder' => 'Confirme la contraseña',
                    'required' => !isset($employees),
                    'autocomplete' => 'new-password',
                    'id' => 'password_confirmation'
                ]) !!}
                <small id="passwordMatchError" class="text-danger d-none">Las contraseñas no coinciden</small>
            </div>
            <!-- Tipo de empleado -->
            <div class="form-group col-md-6">
                {!! Form::label('type_id', 'Tipo de Empleado') !!}
                <select name="type_id" id="employeeTypesSelect" class="form-control" required>
                    <option value="">Seleccione un tipo</option>
                    @foreach ($employeetypes as $id => $name)
                        <option value="{{ $id }}" {{ isset($employees) && $employees->type_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Licencia -->
            <div class="form-group col-md-6" id="licenseContainer" style="display: none;">
                {!! Form::label('license', 'Licencia') !!}
                {!! Form::select('license', [
                    'A-I' => 'A-I: Vehículos particulares (sedanes, SUVs, furgonetas)',
                    'A-IIa' => 'A-IIa: Taxis, ambulancias, transporte público ligero',
                    'A-IIb' => 'A-IIb: Microbuses y minibuses',
                    'A-IIIa' => 'A-IIIa: Ómnibus interurbanos',
                    'A-IIIb' => 'A-IIIb: Camiones pesados, volquetes',
                    'A-IIIc' => 'A-IIIc: Todo tipo de vehículos pesados',
                    'B-I' => 'B-I: Triciclos no motorizados (transporte especial)',
                    'B-IIa' => 'B-IIa: Bicimotos',
                    'B-IIb' => 'B-IIb: Motocicletas y motocicletas con sidecar',
                    'B-IIc' => 'B-IIc: Mototaxis y trimotos'
                ], null, ['class' => 'form-control', 'placeholder' => 'Seleccione una licencia']) !!}
            </div>
        </div>
    </div>
    <!-- Columna derecha: Foto del empleado -->
    <div class="col-lg-4 mb-4">
        <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(event)">
        @php
            $photoPath = 'storage/brands/empty.png';
            if (isset($employees) && !empty($employees->photo)) {
                $photoPath = $employees->photo;
            }
        @endphp
        <div class="border rounded p-2" style="cursor: pointer;"
            onclick="document.getElementById('photoInput').click();">
            <img id="photoPreview" src="{{ asset($photoPath) }}" alt="Foto del Empleado"
                class="img-fluid rounded shadow-sm" style="height: 200px; object-fit: cover;">
            <p class="text-muted mt-2 mb-0">Haz clic para cambiar la imagen</p>
        </div>
    </div>
</div>

<script>
    $(function () {
        const typeSelect = $('#employeeTypesSelect');
        const licenseContainer = $('#licenseContainer');
        const licenseSelect = licenseContainer.find('select');
        // Mostrar u ocultar el campo de licencia según el tipo de empleado
        function updateLicenseField() {
            const isDriver = typeSelect.val() === '1'; // tipo_id 1 = Conductor
            if (isDriver) {
                licenseContainer.show();
                licenseSelect.prop('required', true);
            } else {
                licenseContainer.hide();
                licenseSelect.prop('required', false).val('');
            }
        }
        // Ejecutar cuando cambia el tipo de empleado
        typeSelect.on('change', function () {
            updateLicenseField();
        });
        // Ejecutar al cargar si ya hay un tipo seleccionado (modo edición o validación fallida)
        updateLicenseField();
        // Validación de contraseña en tiempo real
        const password = $('#password');
        const passwordConfirmation = $('#password_confirmation');
        const passwordMatchError = $('#passwordMatchError');
        function validatePasswordMatch() {
            if (password.val() !== passwordConfirmation.val()) {
                passwordMatchError.removeClass('d-none');
            } else {
                passwordMatchError.addClass('d-none');
            }
        }
        password.on('input', validatePasswordMatch);
        passwordConfirmation.on('input', validatePasswordMatch);
        // Vista previa de imagen
        $('#photoInput').on('change', function (event) {
            const input = event.target;
            const preview = $('#photoPreview')[0];
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
</script>