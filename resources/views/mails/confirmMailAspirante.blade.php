@extends('layouts.correo-aspirante')

@section('asunto')
  CONFIRMACIÓN DE CORREO ELECTRÓNICO
@endsection

@section('mensaje')
  <p>Haz click en el siguiente enlace para validar tu dirección de correo electrónico.</p>

  @component('mail::button', ['url' => env('APP_URL_FRONTEND') . '/confirmacion-correo/'.$token])
    Confirmar correo
  @endcomponent

  <p>Si no solicito su registro en la plataforma, puede ignorar este correo electrónico.</p>

@endsection