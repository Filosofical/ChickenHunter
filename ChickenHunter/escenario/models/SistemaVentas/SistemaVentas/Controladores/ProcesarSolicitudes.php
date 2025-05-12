<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

// ID del administrador que está aprobando/rechazando
$idAdministrador = $_SESSION['user_id'];

if (isset($_POST['aprobar_producto'])) {
    $idProducto = $_POST['id_producto'];
    $query = "UPDATE producto SET Estado = 'Aprobado', ID_Administrador = ? WHERE ID_Producto = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $idAdministrador, $idProducto);
    mysqli_stmt_execute($stmt);
}

if (isset($_POST['rechazar_producto'])) {
    $idProducto = $_POST['id_producto'];
    $query = "UPDATE producto SET Estado = 'Rechazado', ID_Administrador = ? WHERE ID_Producto = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $idAdministrador, $idProducto);
    mysqli_stmt_execute($stmt);
}

mysqli_close($connection);

header("Location: ../Contenido/Solicitudes.php");
exit();
?>
