<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sistemadeventas";
$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener parámetros
$query = isset($_GET['query']) ? mysqli_real_escape_string($connection, $_GET['query']) : '';
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'nombre';

// Construcción de la consulta
$sql = "";

if ($filtro === "nombre") {
    // Búsqueda general para productos y usuarios
    $sql = "
        SELECT 
            ID_Producto AS ID, 
            Nombre_Producto AS Nombre, 
            Precio_Producto AS Precio, 
            'producto' AS Tipo
        FROM producto 
        WHERE Nombre_Producto LIKE '%$query%'
        
        UNION 
        
        SELECT 
            ID_Usuario AS ID, 
            Nombre_Usuario AS Nombre, 
            NULL AS Precio, 
            'usuario' AS Tipo
        FROM usuario 
        WHERE Nombre_Usuario LIKE '%$query%'
    ";
} else {
    // Filtros específicos solo para productos
    switch ($filtro) {
        case 'menor_precio':
            $sql = "SELECT * FROM producto WHERE Nombre_Producto LIKE '%$query%' ORDER BY Precio_Producto ASC";
            break;
        case 'mayor_precio':
            $sql = "SELECT * FROM producto WHERE Nombre_Producto LIKE '%$query%' ORDER BY Precio_Producto DESC";
            break;
        case 'mas_pedidos':
            $sql = "SELECT * FROM producto WHERE Nombre_Producto LIKE '%$query%' ORDER BY Cantidad_Pedidos DESC";
            break;
        case 'menos_pedidos':
            $sql = "SELECT * FROM producto WHERE Nombre_Producto LIKE '%$query%' ORDER BY Cantidad_Pedidos ASC";
            break;
        default:
            $sql = "SELECT * FROM producto WHERE Nombre_Producto LIKE '%$query%'";
            break;
    }
}

// Ejecutar consulta
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Contenido/Buscar.css">
    <title>Resultados de Búsqueda</title>
</head>
<body>

<!--Inicio NavBar-->
<nav class="navbar" >
      <div class="container">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
               <!--Inicio-->
               <a class="navbar-brand" href="../Contenido/Inicio.php">
                  <h4>FCFM Marketplace</h4>
               </a>
               <!--Productos-->
               <a class="nav-link" aria-current="page" href="../Contenido/Productos.php">
                   <h4>PRODUCTOS</h4>
               </a>
                <!--Categorias-->
                <a class="nav-link" aria-current="page" href="../Contenido/Categorias.php">
                   <h4>CATEGORIAS</h4>
               </a>
               <!--Perfil-->
                <a class="nav-link" href="../Contenido/PerfilUser.php">
                   <h4>PERFIL</h4>
                </a>              
               <!--Listas-->
               <a class="nav-link" href="../Contenido/Listas.php">
                   <h4>LISTAS</h4>
               </a>
               <!--Cesta/Carrito de compra-->
               <a class="nav-link" href="../Contenido/Carrito.php">
                   <h4>CARRITO</h4>
               </a>   
               <!--Cesta/Carrito de compra-->
               <a class="nav-link" href="../Contenido/ChatPrivado.php">
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

    <h1>Resultados de Búsqueda</h1>
    <div class="resultados">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='resultado'>";
            if (isset($row['Tipo']) && $row['Tipo'] === 'producto') {
                echo "<h2>Producto: " . $row['Nombre'] . "</h2>";
                echo "<p>Precio: $" . $row['Precio'] . "</p>";
            } elseif (isset($row['Tipo']) && $row['Tipo'] === 'usuario') {
                echo "<h2>Usuario: " . $row['Nombre'] . "</h2>";
            } else {
                // Para filtros específicos sin 'Tipo'
                echo "<h2>Producto: " . $row['Nombre_Producto'] . "</h2>";
                echo "<p>Precio: $" . $row['Precio_Producto'] . "</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No se encontraron resultados.</p>";
    }
    ?>
    </div>
</body>
</html>
