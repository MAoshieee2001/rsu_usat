<div class="row">
    <!-- Columna izquierda: Datos del empleado -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Datos del Empleado</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $fields = [
                            ['dni', 'DNI', 'text', 'Ingrese el DNI'],
                            ['names', 'Nombres', 'text', 'Ingrese nombres'],
                            ['lastnames', 'Apellidos', 'text', 'Ingrese apellidos'],
                            ['birthday', 'Fecha de Nacimiento', 'date', ''],
                            ['license', 'Licencia', 'text', 'Ingrese la licencia'],
                            ['address', 'Dirección', 'text', 'Ingrese la dirección'],
                            ['email', 'Correo Electrónico', 'email', 'Ingrese el correo'],
                            ['phone', 'Teléfono', 'text', 'Ingrese el teléfono'],
                        ];
                    @endphp

                    @foreach ($fields as [$name, $label, $type, $placeholder])
                        <div class="form-group col-md-6">
                            {!! Form::label($name, $label) !!}
                            {!! Form::$type($name, null, [
                                'class' => 'form-control',
                                'placeholder' => $placeholder,
                                'required',
                                'autocomplete' => 'off'
                            ]) !!}
                        </div>
                    @endforeach

                    <!-- Contraseña -->
                    <div class="form-group col-md-6">
                        {!! Form::label('password', 'Contraseña') !!}
                        {!! Form::password('password', [
                            'class' => 'form-control',
                            'placeholder' => 'Ingrese la contraseña',
                            'required',
                            'autocomplete' => 'new-password'
                        ]) !!}
                    </div>

                    <!-- Estado -->
                    <div class="form-group col-md-6">
                        {!! Form::label('status', 'Estado') !!}
                        {!! Form::select('status', [1 => 'Activo', 0 => 'Inactivo'], null, [
                            'class' => 'form-control',
                            'required'
                        ]) !!}
                    </div>

                    <!-- Tipo de empleado -->
                    <div class="form-group col-md-6">
                        {!! Form::label('type_id', 'Tipo de Empleado') !!}
                        <select name="type_id" id="employeeTypesSelect" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach ($employeetypes as $id => $name)
                                <option value="{{ $id }}" {{ isset($employees) && $employees->type_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna derecha: Foto del empleado -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-secondary text-white text-center">
                <h5 class="mb-0">Foto del Empleado</h5>
            </div>
            <div class="card-body text-center">
                <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(event)">
                
                @php
                    $photoPath = 'storage/brands/empty.png';
                    if (isset($employees) && !empty($employees->photo)) {
                        $photoPath = $employees->photo;
                    }
                @endphp

                <div class="border rounded p-2" style="cursor: pointer;" onclick="document.getElementById('photoInput').click();">
                    <img id="photoPreview" src="{{ asset($photoPath) }}" alt="Foto del Empleado"
                        class="img-fluid rounded shadow-sm" style="height: 200px; object-fit: cover;">
                    <p class="text-muted mt-2 mb-0">Haz clic para cambiar la imagen</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para previsualizar la imagen seleccionada -->
<script>
    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>