<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$datab = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $datab);

if (!$connection) {
    die("No se ha podido conectar a la base de datos: " . mysqli_error($connection));
}

$id_usuario = $_SESSION['user_id']; 


$sql = "SELECT id_lista, nombre_lista, descripcion FROM listas WHERE id_usuario = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="listas.css">
   <title>Listas</title>
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

   <!-- Inicio cuerpo página listas -->
   <p>Bienvenid@, <?= htmlspecialchars($_SESSION['user_name']); ?></p>

   <!-- Crear una nueva lista -->
   <div class="crear-lista">
      <h2>Crear una Lista</h2>
      <form action="../Controladores/crear_lista.php" method="POST">
         <label for="nombre_lista">Nombre de la Lista:</label>
         <input type="text" id="nombre_lista" name="nombre_lista" required>
         
         <label for="descripcion">Descripción:</label>
         <textarea id="descripcion" name="descripcion"></textarea>
         
         <button type="submit">Crear Lista</button>
      </form>
   </div>

   <!-- Listas del usuario -->
   <div class="listas">
   <h2>Mis Listas</h2>
   <?php if ($result->num_rows > 0): ?>
      <?php while ($lista = $result->fetch_assoc()): ?>
         <div class="lista">
            <h3><?= htmlspecialchars($lista['nombre_lista']); ?></h3>
            <p><?= htmlspecialchars($lista['descripcion']); ?></p>
            <a href="../Controladores/eliminar_lista.php?id_lista=<?= $lista['id_lista']; ?>">Eliminar Lista</a>
            
            <!-- Mostrar los productos de la lista -->
            <h4>Productos en esta lista:</h4>
            <?php
            $id_lista = $lista['id_lista'];
            $sql_productos = "
               SELECT p.id_producto, p.nombre_producto, p.descripcion_producto 
               FROM lista_productos lp
               INNER JOIN producto p ON lp.id_producto = p.id_producto
               WHERE lp.id_lista = ?";
            $stmt_productos = $connection->prepare($sql_productos);
            $stmt_productos->bind_param("i", $id_lista);
            $stmt_productos->execute();
            $result_productos = $stmt_productos->get_result();
            ?>

            <?php if ($result_productos->num_rows > 0): ?>
               <ul>
                  <?php while ($producto = $result_productos->fetch_assoc()): ?>
                     <li>
                        <strong><?= htmlspecialchars($producto['nombre_producto']); ?></strong>
                        <p><?= htmlspecialchars($producto['descripcion_producto']); ?></p>
                        <a href="../Controladores/eliminar_producto_lista.php?id_lista=<?= $id_lista; ?>&id_producto=<?= $producto['id_producto']; ?>">Eliminar</a>
                     </li>
                  <?php endwhile; ?>
               </ul>
            <?php else: ?>
               <p>No hay productos en esta lista.</p>
            <?php endif; ?>

            <?php $stmt_productos->close(); ?>
         </div>
      <?php endwhile; ?>
   <?php else: ?>
      <p>No tienes listas creadas.</p>
   <?php endif; ?>
</div>


   <!-- Agregar Producto a una Lista -->
<div class="agregar-producto">
    <h2>Agregar Producto a la Lista</h2>
    <form action="../Controladores/agregar_producto.php" method="POST">
        <label for="id_lista">Selecciona una Lista:</label>
        <select id="id_lista" name="id_lista" required>
            <?php
            // Obtener las listas del usuario para poblar el select
            $sql_listas = "SELECT id_lista, nombre_lista FROM listas WHERE id_usuario = ?";
            $stmt_listas = $connection->prepare($sql_listas);
            $stmt_listas->bind_param("i", $id_usuario);
            $stmt_listas->execute();
            $result_listas = $stmt_listas->get_result();
            while ($lista = $result_listas->fetch_assoc()) {
                echo "<option value='" . $lista['id_lista'] . "'>" . htmlspecialchars($lista['nombre_lista']) . "</option>";
            }
            $stmt_listas->close();
            ?>
        </select>

        <label for="id_producto">ID del Producto:</label>
        <input type="number" id="id_producto" name="id_producto" required>

        <button type="submit">Agregar Producto</button>
    </form>
</div>


<?php
// Cerrar la conexión y liberar recursos
$stmt->close();
$connection->close();
?>
</body>
</html>
