<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // cambia esto si usas contraseña en tu servidor local
$bd = "peluqueria";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $bd);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
