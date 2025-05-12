<?php
$host = "localhost";
$user = "root";
$pass = "";
$datab = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $datab);

if (!$connection) {
    die("No se ha podido conectar a la base de datos: " . mysqli_error($connection));
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insertar una nueva categoría
    $usuario_id = $_SESSION['user_id'];
    $nombreCategoria = mysqli_real_escape_string($connection, $_POST['nombre_categoria']);
    $descripcionCategoria = mysqli_real_escape_string($connection, $_POST['descripcion_categoria']);

    $query = "INSERT INTO categoria (Usuario_ID, Nombre_Categoria, Descripcion_Categoria)
              VALUES ('$usuario_id', '$nombreCategoria', '$descripcionCategoria')";
    if (mysqli_query($connection, $query)) {
        echo "Categoría insertada correctamente";
    } else {
        echo "Error al insertar categoría: " . mysqli_error($connection);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM categoria";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID Categoria</th>
                    <th>ID Usuario</th>
                    <th>Nombre Categoria</th>
                    <th>Descripción Categoria</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['ID_Categoria']}</td>
                    <td>{$row['Usuario_ID']}</td>
                    <td>{$row['Nombre_Categoria']}</td>
                    <td>{$row['Descripcion_Categoria']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron registros.";
    }
}

mysqli_close($connection);
?>
