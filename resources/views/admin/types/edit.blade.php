{!! Form::model($type, ['route' => ['admin.types.update', $type], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.types.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}