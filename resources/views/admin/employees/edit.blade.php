        {!! Form::model($employees, ['route' => ['admin.employees.update', $employees], 'method' => 'PUT', 'files' => true]) !!}
        @include('admin.employees.template.form')
        <button type="submit" class="btn btn-primary">Actualizar</button>
         
        {!! Form::close() !!}