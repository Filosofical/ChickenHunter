<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="productos.css">
   <title>Productos</title>
</head>
<body>
<!--Inicio NavBar-->
<nav class="navbar">
      <div class="container">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
               <a class="navbar-brand" href="Inicio.php"><h4>FCFM Marketplace</h4></a>
               <a class="nav-link" aria-current="page" href="Productos.php"><h4>PRODUCTOS</h4></a>
               <a class="nav-link" aria-current="page" href="Categorias.php"><h4>CATEGORIAS</h4></a>
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
<!--Fin NavBar-->

<!--Inicio cuerpo pagina-->
<h1>Bienvenid@, <?php echo $_SESSION['user_name']; ?></h1>
<h3>Productos disponibles</h3>
<table id="tablaProductos">
    <thead>
        <tr>
            <th>ID Producto</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Tipo de Venta</th>
            <th>Precio</th>
            <th>Cantidad disponible</th>
            <th>Foto</th>
            <th>Video</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

        if (!$connection) {
            die("No se pudo conectar con la base de datos: " . mysqli_error($connection));
        }

        $query = "SELECT p.ID_Producto, p.Nombre_Producto, p.Descripcion_Producto, c.Nombre_Categoria, 
                         p.Tipo_Venta, p.Precio_Producto, p.Cantidad_Producto, 
                         p.Foto_Producto, p.Video_Producto, p.ID_Usuario
                  FROM producto p
                  INNER JOIN categoria c ON p.ID_Categoria = c.ID_Categoria
                  WHERE p.Estado = 'Aprobado'"; 

        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['ID_Producto']}</td>";
                echo "<td>{$row['Nombre_Producto']}</td>";
                echo "<td>{$row['Descripcion_Producto']}</td>";
                echo "<td>{$row['Nombre_Categoria']}</td>";
                echo "<td>{$row['Tipo_Venta']}</td>";
                echo "<td>{$row['Precio_Producto']}</td>";
                echo "<td>{$row['Cantidad_Producto']}</td>";
                echo "<td><img src='../uploads/images/{$row['Foto_Producto']}' width='50' height='50'></td>";
                echo "<td><video width='50' height='50' controls>
                        <source src='../uploads/videos/{$row['Video_Producto']}' type='video/mp4'>
                      </video></td>";
                echo "<td>
                    <button class='button' onclick='agregarCarrito({$row['ID_Producto']})'>Agregar al Carrito</button>
                    <button class='button' onclick='redirigirChat({$row['ID_Producto']}, {$row['ID_Usuario']})'>Cotizar</button>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No hay productos disponibles.</td></tr>";
        }

        mysqli_close($connection);
        ?>
    </tbody>
</table>

<script>
    function agregarCarrito(productoId) {
        fetch('../Controladores/carrito.php', {
            method: 'POST',
            body: JSON.stringify({productoId: productoId}),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto agregado al carrito');
            } else {
                alert('Error al agregar al carrito');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function redirigirChat(productoId, usuarioId) {
        window.location.href = `Chat.php?productoId=${productoId}&usuarioId=${usuarioId}`;
    }
</script>
</body>
</html>

