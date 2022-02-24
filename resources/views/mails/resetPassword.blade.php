@component('mail::message')
# Restablecer tu contraseña

Hola,

Siga este enlace para restablecer tu contraseña.

@component('mail::button', ['url' => env('APP_URL_FRONTEND') . '/reset/'.$token])
Restablecer contraseña
@endcomponent

Si no solicitó restablecer su contraseña, puede ignorar este correo electrónico.


Gracias,<br>
Tu equipo de soporte.
@endcomponent
