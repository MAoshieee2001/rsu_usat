<div class="row">
    <div class="form-group col-6">
        {!! Form::label('contract_id', 'Contrato') !!}
        {!! Form::select('contract_id', $contract, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione un contrato.',
            'required'
        ]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('employee_id', 'Empleado') !!}
        {!! Form::select('employee_id', $employee, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione un empleado.',
            'required'
        ]) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        {!! Form::label('date_start', 'Fecha de inicio') !!}
        {!! Form::date('date_start', null, [
            'class' => 'form-control',
            'required',
            'min' => date('Y-m-d'),
        ]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('date_end', 'Fecha de fin') !!}
        {!! Form::date('date_end', null, [
            'class' => 'form-control',
            'min' => date('Y-m-d'),
        ]) !!}
        <small id="temporal_contract_info" class="text-info d-none">Los contratos temporales tienen una duración fija de 60 días</small>
    </div>
</div>

<script>
    $(function () {
        const select_contract = $('#contract_id');
        const select_date_end = $('#date_end');
        const select_date_start = $('#date_start');
        const temporal_info = $('#temporal_contract_info');
        // Función para verificar si el contrato es temporal
        function isTemporalContract() {
            const contract_text = select_contract.find('option:selected').text();
            return contract_text.toLowerCase().includes('temporal');
        }
        // Función para calcular la fecha de fin +60 días
        function calculateEndDate() {
            const start_date = select_date_start.val();
            if (start_date) {
                const startDate = new Date(start_date);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 60);
                select_date_end.val(endDate);
            }
        }
        // Función para actualizar el estado de los campos según el contrato
        function updateContractFields() {
            if (isTemporalContract()) {
                select_date_end.prop('disabled', true);
                temporal_info.removeClass('d-none');
                calculateEndDate();
            } else {
                select_date_end.prop('disabled', false).val('');
                temporal_info.addClass('d-none');
            }
        }
        // Al cambiar el tipo de contrato
        select_contract.on('change', function () {
            updateContractFields();
        });
        // Al cambiar la fecha de inicio
        select_date_start.on('change', function () {
            if (isTemporalContract()) {
                calculateEndDate();
            }
        });
        // Ejecutar al cargar por si ya hay valores seleccionados
        updateContractFields();
    });
</script>