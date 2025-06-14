{!! Form::model($type, ['route' => ['admin.employeetypes.update', $type], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.employeetypes.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}