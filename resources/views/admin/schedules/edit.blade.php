{!! Form::model($schedule, ['route' => ['admin.schedules.update', $schedule], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.schedules.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}