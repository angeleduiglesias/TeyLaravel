<x-mail::message>
# ¡Hola {{ $documento->tramite->cliente->nombre }}!

Tu documento de **reserva de nombre** ha sido **aprobado** satisfactoriamente.

Puedes continuar con el siguiente paso del proceso desde tu cuenta en la plataforma.

<x-mail::button :url="config('app.url') . '/login'">
Ingresar a la plataforma
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
