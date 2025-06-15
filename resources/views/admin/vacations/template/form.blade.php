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

    // Función segura para sumar días (corrige timezone bugs)
    function addDaysToDate(dateString, days) {
        const parts = dateString.split('-'); // YYYY-MM-DD
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1; // JS usa 0-index en meses
        const day = parseInt(parts[2], 10);

        const date = new Date(year, month, day);
        date.setDate(date.getDate() + (days - 1));

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    $(function () {
        $('#btnGenerarFechaVacaciones').on('click', function () {
            const date_start = $('#date_start').val();
            const today = new Date().toISOString().split('T')[0];

            if (!date_start) {
                get_alerts({
                    title: 'Error!',
                    icon: 'warning',
                    text: 'Debe seleccionar una fecha de inicio.'
                });
                return;
            }

            if (date_start < today) {
                get_alerts({
                    title: 'Error!',
                    icon: 'warning',
                    text: 'La fecha de inicio no puede ser menor a hoy.'
                });
                return;
            }

            const mode = $('#mode').val();
            if (!mode) {
                get_alerts({
                    title: 'Error!',
                    icon: 'warning',
                    text: 'Debe seleccionar la modalidad de vacaciones.'
                });
                return;
            }

            const diasVacaciones = mode === 'QUINCENAL' ? 15 : 30;
            const date_end = addDaysToDate(date_start, diasVacaciones);

            $('#date_end').val(date_end);

            get_alerts({
                title: 'Perfecto!',
                icon: 'success',
                text: `Se generó correctamente la fecha fin: ${date_end}`
            });
        });
    });
</script>
