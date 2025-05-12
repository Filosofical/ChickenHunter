<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="consultar_compras.css">
    <title>Consultar Compras</title>
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

    <h2>Consultar Compras</h2>
    <form method="GET" action="consultar_compras.php">
        <label for="fecha_inicio">Fecha Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio">

        <label for="fecha_fin">Fecha Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin">

        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria">
            <option value="">Todas</option>
            <?php
            // Cargar categorías desde la base de datos
            $connection = mysqli_connect("localhost", "root", "", "sistemadeventas");
            $query = "SELECT * FROM categoria";
            $result = mysqli_query($connection, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['ID_Categoria']}'>{$row['Nombre_Categoria']}</option>";
            }
            mysqli_close($connection);
            ?>
        </select>

        <button type="submit">Buscar</button>
    </form>

    <?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

if (!$connection) {
    die("Error al conectar con la base de datos: " . mysqli_error($connection));
}

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $userId = $_SESSION['user_id'];

        $connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

        $query = "SELECT p.Fecha, c.Nombre_Categoria, pr.Nombre_Producto, p.Calificacion, pr.Precio_Producto
                  FROM pedidos p
                  INNER JOIN producto pr ON p.ID_Producto = pr.ID_Producto
                  INNER JOIN categoria c ON pr.ID_Categoria = c.ID_Categoria
                  WHERE p.ID_Usuario = $userId";

        if ($fechaInicio) {
            $query .= " AND p.Fecha >= '$fechaInicio'";
        }

        if ($fechaFin) {
            $query .= " AND p.Fecha <= '$fechaFin'";
        }

        if ($categoria) {
            $query .= " AND c.ID_Categoria = $categoria";
        }

        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Categoría</th>
                        <th>Producto</th>
                        <th>Calificación</th>
                        <th>Precio</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['Fecha']}</td>
                        <td>{$row['Nombre_Categoria']}</td>
                        <td>{$row['Nombre_Producto']}</td>
                        <td>{$row['Calificacion']}</td>
                        <td>{$row['Precio_Producto']}</td>
                      </tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No se encontraron compras.</p>";
        }

        mysqli_close($connection);
    }
    ?>
</body>
</html>
