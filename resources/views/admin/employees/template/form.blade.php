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
            <div class="form-group col-md-6">
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

<!-- Script para previsualizar la imagen seleccionada y validar contraseña -->
<script>
    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Validación de contraseña en tiempo real
    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordMatchError = document.getElementById('passwordMatchError');

        if (password && passwordConfirmation) {
            [password, passwordConfirmation].forEach(field => {
                field.addEventListener('input', function () {
                    if (password.value !== passwordConfirmation.value) {
                        passwordMatchError.classList.remove('d-none');
                    } else {
                        passwordMatchError.classList.add('d-none');
                    }
                });
            });
        }
    });
</script>