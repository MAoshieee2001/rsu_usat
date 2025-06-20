{!! Form::model($route, ['route' => ['admin.routes.update', $route], 'method' => 'PUT', 'files' => true]) !!}
@include('admin.routes.template.form')
<button type="submit" class="btn btn-primary">Actualizar</button>

{!! Form::close() !!}