<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($usuario !== '' && $asunto !== '' && $mensaje !== '') {
        $consulta = 'INSERT INTO tickets (usuario, asunto, mensaje) VALUES (?, ?, ?)';
        $sentencia = $pdo->prepare($consulta);
        $sentencia->execute([$usuario, $asunto, $mensaje]);
        header('Location: index.php');
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

    <form method="POST">
        <h2>Crear Nuevo Ticket</h2>
        <input type="text" name="usuario" placeholder="Tu nombre o correo" required>
        <input type="text" name="asunto" placeholder="Asunto del problema" required>
        <textarea name="mensaje" rows="4" placeholder="Describe detalladamente tu situación" required></textarea>
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
