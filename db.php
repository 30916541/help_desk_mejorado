<?php
$servidor = 'localhost';
$nombre_bd = 'mesadeayuda';
$usuario_bd = 'proyecto';
$contrasena_bd = 'proyecto';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$nombre_bd;charset=utf8", $usuario_bd, $contrasena_bd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
