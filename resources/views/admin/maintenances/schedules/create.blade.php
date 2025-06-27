{!! Form::open([
    'route' => ['admin.maintenances.schedules.store', $maintenance->id],
    'method' => 'POST',
    'class' => 'ajaxScheduleForm'
]) !!}
    @include('admin.maintenances.schedules.template.form')
    <div class="text-right">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Registrar
        </button>
    </div>
{!! Form::close() !!}
