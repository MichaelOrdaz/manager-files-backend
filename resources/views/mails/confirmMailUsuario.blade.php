@extends('layouts.correo-aspirante')

@section('asunto')
  CONFIRMACIÓN DE CORREO ELECTRÓNICO
@endsection

@section('mensaje')

<p><b>Instrucciones:</b></p>

<p>1. Confirme su correo electrónico</p>
<p>2. Una vez confirmado su correo inicie sesión, su <b>contraseña temporal es <i><span style="color:red;">{{$data['contrasenia']}}</span></i></b> </p>
<p>
  3. Dentro de su cuenta diríjase a su perfil de usuario y cambie su contraseña, <br>
  <b>tenga en cuenta que la información de su cuenta es de índole personal y se tiene que manejar con discreción</b>
</p>
<p>4. Una vez realizados estos pasos ya puede disponer de su cuenta</p>

<p>Haz click en el siguiente enlace para validar tu dirección de correo electrónico.</p>

@component('mail::button', ['url' => env('APP_URL_FRONTEND') . '/confirmacion-correo/'.$token])
  Confirmar correo
@endcomponent

<p>Si no solicito su registro en la plataforma, puede ignorar este correo electrónico.</p>

@endsection