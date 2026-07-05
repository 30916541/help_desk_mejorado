<?php
// ============================================================
// Configuración de conexión a la base de datos
// ============================================================

$servidor = 'localhost';       // Dirección del servidor MySQL
$nombre_bd = 'mesadeayuda';    // Nombre de la base de datos
$usuario_bd = 'proyecto';      // Usuario con permisos
$contrasena_bd = 'proyecto';   // Contraseña del usuario

try {
    // Crear conexión PDO con charset UTF-8
    $pdo = new PDO("mysql:host=$servidor;dbname=$nombre_bd;charset=utf8", $usuario_bd, $contrasena_bd);
    // Configurar PDO para lanzar excepciones en errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Detener ejecución si falla la conexión
    die('Error de conexión: ' . $e->getMessage());
}
