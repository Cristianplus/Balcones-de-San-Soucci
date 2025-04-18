<?php
// Parámetros de conexión a la base de datos
$host = "localhost";
$usuario = "root";
$contraseña = "";
$baseDeDatos = "condominio";

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $baseDeDatos);
$conn->set_charset("utf8mb4");

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar el conjunto de caracteres
$conn->set_charset("utf8");

?>