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

// Consultas para obtener productos y categorías pendientes
$queryProductos = "SELECT * FROM producto WHERE Estado = 'Pendiente'";
$resultProductos = mysqli_query($connection, $queryProductos);

$queryCategorias = "SELECT * FROM categoria WHERE Estado = 'Pendiente'";
$resultCategorias = mysqli_query($connection, $queryCategorias);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Solicitudes.css">
    <title>Solicitudes de productos</title>
</head>
<body>
    <!-- Inicio NavBar -->
    <nav class="navbar">
        <div class="container">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                <a class="navbar-brand" href="Inicio.php"><h4>FCFM Marketplace</h4></a>
                <a class="nav-link" href="Productos.php"><h4>PRODUCTOS</h4></a>
                <a class="nav-link" href="Categorias.php"><h4>CATEGORIAS</h4></a>
                <a class="nav-link" href="PerfilUser.php"><h4>PERFIL</h4></a>
                <a class="nav-link" href="Listas.php"><h4>LISTAS</h4></a>
                <a class="nav-link" href="Carrito.php"><h4>CARRITO</h4></a>
                <a class="nav-link" href="ChatPrivado.php"><h4>CHAT</h4></a>
                <a class="nav-link" href="../Login/Login.php"><h4>SALIR</h4></a>
                <form class="d-flex form2">
                    <input class="input input2" type="search" placeholder="Buscar...">
                    <button class="button button2" type="submit"><h4>BUSCAR</h4></button>
                </form>
            </ul>
        </div>
    </nav>
    <!-- Fin NavBar -->

    <!-- Inicio cuerpo página -->
    <h1>Bienvenid@, <?php echo $_SESSION['user_name']; ?></h1>
    <div class="contenedor-solicitudes">
        <h2>Solicitudes de productos pendientes</h2>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = mysqli_fetch_assoc($resultProductos)) { ?>
                    <tr>
                        <td><?php echo $producto['Nombre_Producto']; ?></td>
                        <td><?php echo $producto['Descripcion_Producto']; ?></td>
                        <td><?php echo $producto['Precio_Producto']; ?></td>
                        <td>
                            <form method="POST" action="../Controladores/ProcesarSolicitudes.php">
                                <input type="hidden" name="id_producto" value="<?php echo $producto['ID_Producto']; ?>">
                                <button type="submit" name="aprobar_producto" class="boton aprobar">Aprobar</button>
                                <button type="submit" name="rechazar_producto" class="boton rechazar">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Fin cuerpo página -->
</body>
</html>
