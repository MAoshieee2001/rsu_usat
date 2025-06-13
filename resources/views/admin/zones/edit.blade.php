{!! Form::model($zones, ['route' => ['admin.zones.update', $zones], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.zones.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}