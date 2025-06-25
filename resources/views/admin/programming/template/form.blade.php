<div class="row">
    <div class="form-group col-md-6">
        {!! Form::label('zone_id', 'Zona') !!}
        {!! Form::select('zone_id', $zonas, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una zona',
            'required',
            'id' => 'zone_id'
        ]) !!}
    </div>

    <div class="form-group col-md-6">
        {!! Form::label('schedule_id', 'Horario') !!}
        {!! Form::select('schedule_id', $horarios, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione un horario',
            'required',
            'id' => 'schedule_id'
        ]) !!}
    </div>
    <div class="form-group col-md-6">
        {!! Form::label('modality_id', 'Modalidad') !!}
        {!! Form::select('modality_id', $modality, null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una modalidad',
            'required',
            'id' => 'modality_id'
        ]) !!}
    </div>


    <div class="form-group col-md-3">
        {!! Form::label('date_start', 'Fecha de inicio') !!}
        {!! Form::date('date_start', null, ['class' => 'form-control', 'required']) !!}
    </div>

    <div class="form-group col-md-3">
        {!! Form::label('date_end', 'Fecha de fin') !!}
        {!! Form::date('date_end', null, ['class' => 'form-control', 'required']) !!}
    </div>
</div>

@push('js')
<script>
</script>
@endpush