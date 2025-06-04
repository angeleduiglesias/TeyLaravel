<!DOCTYPE html>
<html lang="en">
<body>
    <p>Hola {{ $cliente->nombre }} {{ $cliente->apellidos }},</p>
    <p>Te informamos que el nombre de tu empresa <strong>{{ $empresa->nombre_empresa }}</strong> ha sido aprobado.</p>
    <p>¡Gracias por confiar en nosotros!</p>
</body>
</html>