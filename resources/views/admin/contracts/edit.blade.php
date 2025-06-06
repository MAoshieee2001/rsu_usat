        {!! Form::model($employeecontract, ['route' => ['admin.contracts.update', $employeecontract], 'method' => 'PUT', 'files' => true]) !!}
        @include('admin.contracts.template.form')
        <button type="submit" class="btn btn-primary">Actualizar</button>
         
        {!! Form::close() !!}