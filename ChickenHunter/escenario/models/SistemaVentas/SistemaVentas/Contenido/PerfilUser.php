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
$dbname = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}


$user_id = $_SESSION['user_id'];
$sql = "SELECT Nombre_Usuario, Foto_Perfil, Rol_Usuario FROM usuario WHERE ID_Usuario = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $user_name = $row['Nombre_Usuario'];
    $profile_image = $row['Foto_Perfil']; 
    $user_role = $row['Rol_Usuario'];
} else {
    echo "Error: No se encontró el usuario.";
    exit();
}

// Variables para contenido dinámico
$content = "";
$profile_type = isset($_POST['tipo_perfil']) ? $_POST['tipo_perfil'] : "Privado";

// Lógica según el tipo de perfil
if ($profile_type === "Privado") {
    $content = "<p>Tu perfil es privado. Solo tu nombre de usuario y tu imagen de perfil serán visibles.</p>";
} elseif ($profile_type === "Publico") {
    // Consultar las listas del usuario
    $sql = "SELECT * FROM listas WHERE ID_Usuario = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $content = "<h3>Listas creadas:</h3><ul>";
        while ($list = mysqli_fetch_assoc($result)) {
            $content .= "<li>" . $list['nombre_lista'] . ": " . $list['descripcion'] . "</li>";
        }
        $content .= "</ul>";
    } else {
        $content = "<p>No se encontraron listas.</p>";
    }

// Lógica según el rol del usuario
if ($user_role === "Vendedor") {
    // Mostrar productos aprobados para los vendedores
    $sql = "SELECT * FROM producto WHERE ID_Usuario = ? AND Estado = 'Aprobado'";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $content .= "<h3>Productos aprobados:</h3><ul>";
    while ($product = mysqli_fetch_assoc($result)) {
        $content .= "<li>" . $product['Nombre_Producto'] . "</li>";
    }
    $content .= "</ul>";
    $content .= "<a href='ConsultaVentas.php' class='button'>Consulta de ventas</a>";
    $content .= "<a href='ListadoProductos.php' class='button'>Listado de productos disponibles</a>";
}elseif ($user_role === "Administrador") {
    // Mostrar productos autorizados por el administrador
    $sql = "SELECT * FROM producto WHERE ID_Administrador = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $content .= "<h3>Productos gestionados por ti:</h3><ul>";
    while ($product = mysqli_fetch_assoc($result)) {
        $content .= "<li>" . $product['Nombre_Producto'] . "</li>";
    }
    $content .= "</ul>";
}

}

// Cerramos la conexión
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="PerfilUser.css">
   <title>Perfil de usuario</title>
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

<div class="profile-container">
    <h2>Perfil de usuario</h2>
    <div class="profile-info">
        <img src="<?php echo $profile_image; ?>" alt="Foto de <?php echo $user_name; ?>" class="profile-img">
        <h3><?php echo $user_name; ?></h3>
    </div>

    <form method="POST" action="">
        <label for="tipo_perfil">Tipo de perfil:</label>
        <select name="tipo_perfil" id="tipo_perfil" onchange="this.form.submit()">
            <option value="Privado" <?php if ($profile_type === "Privado") echo "selected"; ?>>Privado</option>
            <option value="Publico" <?php if ($profile_type === "Publico") echo "selected"; ?>>Público</option>
        </select>
    </form>

    <div class="profile-content">
        <?php echo $content; ?>
    </div>
</div>
<script src="PerfilUser.js"></script> 
<script src="PerfilUserValidacion.js"></script> 
</body>
</html>
