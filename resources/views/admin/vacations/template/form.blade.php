<div class="row">
    <div class="form-group col-12">
        {!! Form::label('employee_id', 'Empleado') !!}
        {!! Form::select('employee_id', $employee, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione un empleado.',
    'required'
]) !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-12">
        {!! Form::label('mode', 'Modalidad de Vacaciones') !!}
        {!! Form::select('mode', [
            'MENSUAL' => 'Mensual (30 días)',
            'QUINCENAL' => 'Quincenal (15 días)',
        ], null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione modalidad',
            'required'
        ]) !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-5">
        {!! Form::label('date_start', 'Fecha de inicio') !!}
        {!! Form::date('date_start', null, [
            'class' => 'form-control',
            'required',
            'min' => date('Y-m-d'),
        ]) !!}
    </div>

    <div class="form-group col-5">
        {!! Form::label('date_end', 'Fecha de fin') !!}
        {!! Form::date('date_end', null, [
            'class' => 'form-control',
            'required',
            'min' => date('Y-m-d'),
        ]) !!}
    </div>

    <div class="form-group col-2 d-flex align-items-end">
        <button type="button" class="btn btn-secondary btn-sm w-100" id="btnGenerarFechaVacaciones">
            <i class="fas fa-stopwatch"></i>
        </button>
    </div>
</div>

<script>
    function get_alerts(args) {
        Swal.fire({
            title: args.title,
            icon: args.icon,
            timer: 2000,
            timerProgressBar: true,
            text: args.text,
            draggable: true
        });
    }

    $(function () {

        $('#btnGenerarFechaVacaciones').on('click', function () {
            let date_start = $('#date_start').val();
            let today = new Date().toISOString().split('T')[0];  // 'YYYY-MM-DD'

            if (!date_start) {
                get_alerts({
                    title: 'error!',
                    icon: 'warning',
                    text: 'Debe seleccionar una fecha inicio, para programar las vacaciones'
                });
                e.preventDefault(); // * evitar que haga otra cosa
                return;
            }

            if (date_start < today) {
                get_alerts({
                    title: 'error!',
                    icon: 'warning',
                    text: 'La fecha de inicio no puede ser menor a hoy.'
                });
                e.preventDefault();
                return;
            }

            let startDate = new Date(date_start);
            // Detectar modalidad seleccionada
            let mode = $('#mode').val();
            let diasVacaciones = mode === 'QUINCENAL' ? 15 : 30;

            startDate.setDate(startDate.getDate() + diasVacaciones - 1);

            // Convertimos a 'YYYY-MM-DD' para el input date
            let year = startDate.getFullYear();
            let month = (startDate.getMonth() + 1).toString().padStart(2, '0');
            let day = startDate.getDate().toString().padStart(2, '0');
            let date_end = `${year}-${month}-${day}`;

            get_alerts({
                title: 'Perfecto!',
                icon: 'success',
                text: 'Se genero la programación de la vacaciones.'
            });
            // Seteamos el valor en el input date_end
            $('#date_end').val(date_end);

        });
    });
</script>