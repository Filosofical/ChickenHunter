<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$datab = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $datab);

if (!$connection) {
    die("No se ha podido conectar a la base de datos: " . mysqli_error($connection));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_lista = intval($_GET['id_lista']);

    // Eliminar los productos de la lista
    $query_productos = "DELETE FROM lista_productos WHERE id_lista = ?";
    $stmt_productos = $connection->prepare($query_productos);
    $stmt_productos->bind_param("i", $id_lista);
    $stmt_productos->execute();
    $stmt_productos->close();

    // Eliminar la lista
    $query_lista = "DELETE FROM listas WHERE id_lista = ?";
    $stmt_lista = $connection->prepare($query_lista);
    $stmt_lista->bind_param("i", $id_lista);

    if ($stmt_lista->execute()) {
        echo "Lista eliminada correctamente.";
        header("Location: ../Contenido/Listas.php");
        exit();
    } else {
        echo "Error al eliminar la lista: " . $stmt_lista->error;
    }
}

$stmt_lista->close();
$connection->close();
?>

