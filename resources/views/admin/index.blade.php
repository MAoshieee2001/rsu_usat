@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Proyecto RSU   </h1>
@stop

@section('content')
    <p>Bienvenidos al panel de administración</p>
    Hola javier..
    Hola mundo!
    Hola practica lo que debía saber
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop