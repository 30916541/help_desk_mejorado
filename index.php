<?php
require 'db.php';

$errores = [];
$usuario = $asunto = $mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($usuario === '') {
        $errores['usuario'] = 'El usuario es obligatorio.';
    } elseif (strlen($usuario) < 3) {
        $errores['usuario'] = 'El usuario debe tener al menos 3 caracteres.';
    } elseif (strlen($usuario) > 100) {
        $errores['usuario'] = 'El usuario no puede tener m\u00e1s de 100 caracteres.';
    } elseif (!preg_match('/^[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ@._\-\']+$/', $usuario)) {
        $errores['usuario'] = 'El usuario solo puede contener letras, n\u00fameros, espacios, @, ., -, _';
    }

    if ($asunto === '') {
        $errores['asunto'] = 'El asunto es obligatorio.';
    } elseif (strlen($asunto) < 5) {
        $errores['asunto'] = 'El asunto debe tener al menos 5 caracteres.';
    } elseif (strlen($asunto) > 255) {
        $errores['asunto'] = 'El asunto no puede tener m\u00e1s de 255 caracteres.';
    }

    if ($mensaje === '') {
        $errores['mensaje'] = 'El mensaje es obligatorio.';
    } elseif (strlen($mensaje) < 10) {
        $errores['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
    } elseif (strlen($mensaje) > 500) {
        $errores['mensaje'] = 'El mensaje no puede tener m\u00e1s de 500 caracteres.';
    }

    if (empty($errores)) {
        $consulta = 'INSERT INTO tickets (usuario, asunto, mensaje) VALUES (?, ?, ?)';
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$usuario, $asunto, $mensaje]);
        header('Location: index.php?registrado=1');
        exit;
    }
}

$sentencia = $pdo->query('SELECT * FROM tickets ORDER BY fecha_creacion DESC');
$incidencias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa de Ayuda - Gestión de Incidencias</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Mesa de Ayuda - Gestión de Incidencias</h1>

    <?php if (isset($_GET['registrado'])): ?>
        <div class="mensaje-exito">Ticket registrado exitosamente.</div>
    <?php endif; ?>

    <form method="POST">
        <h2>Crear Nuevo Ticket</h2>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Tu nombre o correo" value="<?php echo htmlspecialchars($usuario); ?>" required>
        <?php if (isset($errores['usuario'])): ?>
            <div class="mensaje-error"><?php echo $errores['usuario']; ?></div>
        <?php endif; ?>

        <label for="asunto">Asunto</label>
        <input type="text" id="asunto" name="asunto" placeholder="Asunto del problema" value="<?php echo htmlspecialchars($asunto); ?>" required>
        <?php if (isset($errores['asunto'])): ?>
            <div class="mensaje-error"><?php echo $errores['asunto']; ?></div>
        <?php endif; ?>

        <label for="mensaje">Mensaje</label>
        <textarea name="mensaje" id="mensaje" rows="4" placeholder="Describe detalladamente tu situación" required><?php echo htmlspecialchars($mensaje); ?></textarea>
        <?php if (isset($errores['mensaje'])): ?>
            <div class="mensaje-error"><?php echo $errores['mensaje']; ?></div>
        <?php endif; ?>

        <button type="submit">Enviar Solicitud</button>
    </form>

    <hr>

    <h2>Incidencias Registradas</h2>

    <?php if (empty($incidencias)): ?>
        <p>No hay incidencias registradas actualmente.</p>
    <?php endif; ?>

    <?php foreach ($incidencias as $incidencia): ?>
        <div class="tarjeta-incidencia">
            <span class="estado">[<?php echo htmlspecialchars($incidencia['estatus']); ?>]</span>
            <strong><?php echo htmlspecialchars($incidencia['asunto']); ?></strong>
            <p class="meta">Enviado por: <?php echo htmlspecialchars($incidencia['usuario']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($incidencia['mensaje'])); ?></p>
            <small class="fecha"><?php echo htmlspecialchars($incidencia['fecha_creacion']); ?></small>
        </div>
    <?php endforeach; ?>

</body>
</html>
