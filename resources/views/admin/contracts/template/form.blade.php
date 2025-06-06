<div class="row">
    <div class="form-group col-6">
        {!! Form::label('contract_id', 'Contracto') !!}
        {!! Form::select('contract_id', $contract, null, [
    'class' => 'form-control',
    'placeholder' => 'Seleccione un contracto.',
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
    </div>
</div>

<script>
    $(function () {

        select_contract = $('#contract_id');
        select_date_end = $('#date_end');

        let contract_id = parseInt(select_contract.val());
        if ([0, 1, 2].includes(contract_id)) {
            select_date_end.prop('disabled', true).val('');
        } else {
            select_date_end.prop('disabled', false);
        }


        select_contract.on('change', function (evt) {
            evt.preventDefault();
            let contract_id = parseInt(select_contract.val());
            if ([0, 1, 2].includes(contract_id)) {
                select_date_end.prop('disabled', true).val('');
                return;
            }

            select_date_end.prop('disabled', false);  // Habilitar
        });
    });
</script>