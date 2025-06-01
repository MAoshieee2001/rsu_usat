        {!! Form::model($vehicle, ['route' => ['admin.vehicles.update', $vehicle], 'method' => 'PUT', 'files' => true]) !!}
        @include('admin.vehicles.template.form')
        <button type="submit" class="btn btn-primary">Actualizar</button>
         
        {!! Form::close() !!}