<div class="row">
    <!-- Columna izquierda: todo menos imagen y descripción -->
    <div class="col-md-8">
        <div class="row">
            <div class="form-group col-4">
                {!! Form::label('code', 'Código del vehiculo') !!}
                {!! Form::text('code', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el código del vehiculo.',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>
            <div class="form-group col-4">
                {!! Form::label('plate', 'Placa del vehiculo') !!}
                {!! Form::text('plate', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la placa del vehiculo.',
                    'required',
                    'autocomplete' => 'off'
                ]) !!}
            </div>
            <div class="form-group col-md-4">
                {!! Form::label('year', 'Año del vehiculo') !!}
                {!! Form::number('year', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el año del vehículo',
                    'required',
                    'min' => 1900,
                    'max' => date('Y'),
                ]) !!}
            </div>
            <div class="form-group col-md-4">
                {!! Form::label('type_id', 'Tipo') !!}
                {!! Form::select('type_id', $type, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione un tipo.',
                    'required'
                ]) !!}
            </div>
            <div class="form-group col-4">
                {!! Form::label('brand_id', 'Marca') !!}
                <select name="brand_id" id="brandSelect" class="form-control" required>
                    <option value="">Seleccione una marca</option>
                    @foreach ($brand as $id => $name)
                        <option value="{{ $id }}" {{ isset($vehicle) && $vehicle->brand_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-4">
                {!! Form::label('model_id', 'Modelo') !!}
                <select name="model_id" id="modelSelect" class="form-control" required>
                    <option value="">Seleccione un modelo</option>
                    @foreach ($model as $id => $name)
                        <option value="{{ $id }}" {{ isset($vehicle) && $vehicle->model_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>



            <div class="form-group col-12">
                {!! Form::label('name', 'Nombre del vehiculo') !!}
                {!! Form::text('name', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese el nombre del vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
            </div>

            <div class="form-group col-4">
                {!! Form::label('color_id', 'Color') !!}
                {!! Form::select('color_id', $color, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione un color.',
    'required'
]) !!}
            </div>
            <div class="form-group col-md-4">
                {!! Form::label('capacity', 'N° Personal') !!}
                {!! Form::number('capacity', null, [
    'class' => 'form-control',
    'placeholder' => 'Capacidad de personal del vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
            </div>

            <div class="form-group col-4">
                {!! Form::label('fuel_capacity', 'Combustible del vehiculo') !!}
                {!! Form::number('fuel_capacity', null, [
    'class' => 'form-control',
    'placeholder' => 'Capacidad de combustible del vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
            </div>

            <div class="form-group col-4">
                {!! Form::label('compaction_capacity', 'Compatación del vehiculo') !!}
                {!! Form::number('compaction_capacity', null, [
    'class' => 'form-control',
    'placeholder' => 'Capacidad de compatación del vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
            </div>

            <div class="form-group col-4">
                {!! Form::label('load_capacity', 'Carga del vehiculo') !!}
                {!! Form::number('load_capacity', null, [
    'class' => 'form-control',
    'placeholder' => 'Capacidad de carga del vehiculo.',
    'required',
    'autocomplete' => 'off'
]) !!}
            </div>

            <div class="form-group col-4">
                {!! Form::label('status', 'Estado del vehículo') !!}
                {!! Form::select('status', [
    1 => 'Activo',
    0 => 'Inactivo',
    2 => 'En mantenimiento'
], null, [
    'class' => 'form-control',
    'required'
]) !!}
            </div>


        </div>
    </div>

    <!-- Columna derecha: imagen y descripción -->
    <div class="col-md-4">
        <div class="form-group text-center">
            <label for="image">Imagen:</label>
            <input type="file" id="imgInput" name="image" accept="image/*" class="form-control-file d-none">
            <input type="hidden" name="profile" value="main">

            <div class="p-2 mt-2" style="border: 1px solid #ccc;">
                @php
                    $logoPath = 'storage/brands/empty.png'; // valor por defecto

                    if (isset($vehicle) && $vehicle->images->isNotEmpty()) {
                        $firstImage = $vehicle->images->last();
                        if ($firstImage && !empty($firstImage->image)) {
                            $logoPath = $firstImage->image;
                        }
                    }
                @endphp
                <img id="imageButton" src="{{ asset($logoPath) }}" alt="Logo de la Marca" class="img-fluid"
                    style="width: 100%; height: 155px; object-fit: contain; cursor: pointer;">
                <p class="text-center mt-2">Seleccione una imagen</p>
            </div>
        </div>

        <div class="form-group mt-3">
            {!! Form::label('description', 'Descripción del vehiculo') !!}
            {!! Form::textarea('description', null, [
    'class' => 'form-control',
    'placeholder' => 'Ingrese la descripción del vehiculo.',
    'style' => 'resize:none',
    'rows' => 4
]) !!}
        </div>
    </div>
</div>

<script>
    document.getElementById('imageButton').addEventListener('click', function () {
        document.getElementById('imgInput').click();
    });

    document.getElementById('imgInput').addEventListener('change', function () {
        const file = this.files[0];

        if (!file || !(file instanceof Blob)) {
            console.warn("No se seleccionó un archivo válido.");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imageButton').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    $('#brandSelect').on('change', function () {
        const brandId = $(this).val();
        $('#modelSelect').html('<option value="">Cargando...</option>');

        if (brandId) {
            $.get(`/admin/models-by-brand/${brandId}`, function (data) {
                let options = '<option value="">Seleccione un modelo</option>';
                $.each(data, function (id, name) {
                    options += `<option value="${id}">${name}</option>`;
                });
                $('#modelSelect').html(options);
            });
        } else {
            $('#modelSelect').html('<option value="">Seleccione un modelo</option>');
        }
    });
</script>