<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Vendedor') {
    header("Location: AccesoDenegado.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

if (!$connection) {
    die("No se pudo conectar con la base de datos: " . mysqli_error($connection));
}

$categoria = $_GET['categoria'] ?? null;
$query = "SELECT p.ID_Producto, p.Nombre_Producto, p.Cantidad_Producto, p.Precio_Producto, c.Nombre_Categoria 
          FROM producto p 
          INNER JOIN categoria c ON p.ID_Categoria = c.ID_Categoria 
          WHERE p.ID_Usuario = {$_SESSION['user_id']}";

if ($categoria) {
    $query .= " AND p.ID_Categoria = $categoria";
}

$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ListadoProductos.css">
    <title>Listado de productos actuales y existencias disponibles</title>
</head>
<body>

 <!--Inicio NavBar-->
 <nav class="navbar" >
      <div class="container">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
               <!--Inicio-->
               <a class="navbar-brand" href="Inicio.php">
                  <h4>FCFM Marketplace</h4>
               </a>
               <!--Productos-->
               <a class="nav-link" aria-current="page" href="Productos.php">
                   <h4>PRODUCTOS</h4>
               </a>
                <!--Categorias-->
                <a class="nav-link" aria-current="page" href="Categorias.php">
                   <h4>CATEGORIAS</h4>
               </a>
               <!--Perfil-->
                <a class="nav-link" href="PerfilUser.php">
                   <h4>PERFIL</h4>
                </a>              
               <!--Listas-->
               <a class="nav-link" href="Listas.php">
                   <h4>LISTAS</h4>
               </a>
               <!--Cesta/Carrito de compra-->
               <a class="nav-link" href="Carrito.php">
                   <h4>CARRITO</h4>
               </a>   
               <!--Cesta/Carrito de compra-->
               <a class="nav-link" href="ChatPrivado.php">
                   <h4>CHAT</h4>
               </a>            
               <!--Log Out-->
               <a class="nav-link" href="../Login/Login.php">
                   <h4>SALIR</h4>
               </a>
               <!--Barra de busqueda-->
               <form class="d-flex form2" method="GET" action="../Controladores/Buscar.php">
               <input class="input input2" type="search" name="query" placeholder="Buscar productos o usuarios..." required>
               <select name="filtro" class="select-filtro">
               <option value="nombre">Nombre</option>
               <option value="menor_precio">Menor Precio</option>
               <option value="mayor_precio">Mayor Precio</option>
               <option value="mas_pedidos">Más Pedidos</option>
               <option value="menos_pedidos">Menos Pedidos</option>
               </select>
               <button class="button button2" type="submit">
               <h4>BUSCAR</h4>
               </button>
               </form>
   
            </ul>
      </div>
    </nav>
   <!--Fin NavBar-->

    <h3>Listado de productos actuales y existencias disponibles</h3>
    <form method="GET">
        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria">
            <option value="">Todas</option>
            <?php
            $queryCategorias = "SELECT * FROM categoria";
            $categorias = mysqli_query($connection, $queryCategorias);
            while ($row = mysqli_fetch_assoc($categorias)) {
                $selected = ($row['ID_Categoria'] == $categoria) ? 'selected' : '';
                echo "<option value='{$row['ID_Categoria']}' $selected>{$row['Nombre_Categoria']}</option>";
            }
            ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Cantidad Disponible</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['Nombre_Producto']}</td>";
                echo "<td>{$row['Nombre_Categoria']}</td>";
                echo "<td>{$row['Cantidad_Producto']}</td>";
                echo "<td>{$row['Precio_Producto']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
mysqli_close($connection);
?>
