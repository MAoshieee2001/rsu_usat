{!! Form::model($schedule, [
    'route' => ['admin.maintenances.schedules.update', $maintenance->id, $schedule->id],
    'method' => 'PUT',
    'class' => 'ajaxScheduleForm'
]) !!}
    @include('admin.maintenances.schedules.template.form')
    <div class="text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
    </div>
{!! Form::close() !!}
