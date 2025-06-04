{!! Form::model($vacation, ['route' => ['admin.vacations.update', $vacation], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.vacations.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}