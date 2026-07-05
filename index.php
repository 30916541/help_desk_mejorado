<?php
require 'db.php';

$errores = [];
$usuario = $asunto = $mensaje = '';
$editarTicket = null;

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $consulta = 'DELETE FROM tickets WHERE id = ?';
    $sentencia = $pdo->prepare($consulta);
    $sentencia->execute([$id]);
    header('Location: index.php?eliminado=1');
    exit;
}

if (isset($_GET['editar'])) {
    $id = (int) $_GET['editar'];
    $consulta = 'SELECT * FROM tickets WHERE id = ?';
    $sentencia = $pdo->prepare($consulta);
    $sentencia->execute([$id]);
    $editarTicket = $sentencia->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $id      = $_POST['id'] ?? '';

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
        if ($id === '') {
            $consulta = 'INSERT INTO tickets (usuario, asunto, mensaje) VALUES (?, ?, ?)';
            $sentencia = $pdo->prepare($consulta);
            $sentencia->execute([$usuario, $asunto, $mensaje]);
            header('Location: index.php?registrado=1');
        } else {
            $estatus = trim($_POST['estatus'] ?? 'Abierto');
            $consulta = 'UPDATE tickets SET usuario = ?, asunto = ?, mensaje = ?, estatus = ? WHERE id = ?';
            $sentencia = $pdo->prepare($consulta);
            $sentencia->execute([$usuario, $asunto, $mensaje, $estatus, $id]);
            header('Location: index.php?actualizado=1');
        }
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
    <?php elseif (isset($_GET['actualizado'])): ?>
        <div class="mensaje-exito">Ticket actualizado exitosamente.</div>
    <?php elseif (isset($_GET['eliminado'])): ?>
        <div class="mensaje-exito">Ticket eliminado exitosamente.</div>
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
            <div class="acciones">
                <a href="?editar=<?php echo $incidencia['id']; ?>" class="btn-editar">Editar</a>
                <a href="?eliminar=<?php echo $incidencia['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Est\u00e1s seguro de eliminar este ticket?')">Eliminar</a>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($editarTicket): ?>
    <div id="modal-editar" class="modal">
        <div class="modal-contenido">
            <h2>Editar Ticket</h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $editarTicket['id']; ?>">

                <label for="modal-usuario">Usuario</label>
                <input type="text" id="modal-usuario" name="usuario" value="<?php echo htmlspecialchars($editarTicket['usuario']); ?>" required>

                <label for="modal-asunto">Asunto</label>
                <input type="text" id="modal-asunto" name="asunto" value="<?php echo htmlspecialchars($editarTicket['asunto']); ?>" required>

                <label for="modal-mensaje">Mensaje</label>
                <textarea id="modal-mensaje" name="mensaje" rows="4" required><?php echo htmlspecialchars($editarTicket['mensaje']); ?></textarea>

                <label for="modal-estatus">Estatus</label>
                <select id="modal-estatus" name="estatus">
                    <option value="Abierto" <?php echo $editarTicket['estatus'] === 'Abierto' ? 'selected' : ''; ?>>Abierto</option>
                    <option value="Cerrado" <?php echo $editarTicket['estatus'] === 'Cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                </select>

                <div class="modal-botones">
                    <button type="submit">Guardar Cambios</button>
                    <a href="index.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
