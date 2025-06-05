{!! Form::model($color, ['route' => ['admin.colors.update', $color], 'method' => 'PUT']) !!}
    @include('admin.colors.template.form')
    <button type="submit" class="btn btn-primary">Actualizar</button>
{!! Form::close() !!}
