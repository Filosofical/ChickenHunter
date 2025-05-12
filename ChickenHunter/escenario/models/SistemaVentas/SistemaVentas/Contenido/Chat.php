<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$productoId = isset($_GET['productoId']) ? $_GET['productoId'] : null;
$usuarioId = isset($_GET['usuarioId']) ? $_GET['usuarioId'] : null;

// Verifica que los parámetros estén presentes
if (!$productoId || !$usuarioId) {
    die("Datos insuficientes para iniciar el chat.");
}

$connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

if (!$connection) {
    die("No se pudo conectar con la base de datos: " . mysqli_error($connection));
}

// Consulta para obtener detalles del producto
$query = "SELECT p.Nombre_Producto, p.Descripcion_Producto, p.Precio_Producto, u.Nombre_Usuario
          FROM producto p
          INNER JOIN usuario u ON p.ID_Usuario = u.ID_Usuario
          WHERE p.ID_Producto = $productoId AND u.ID_Usuario = $usuarioId";

$result = mysqli_query($connection, $query);
$producto = mysqli_fetch_assoc($result);

if (!$producto) {
    die("Producto no encontrado.");
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="chat.css">
   <title>Cotizacion</title>
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
               <form class="d-flex form2">
                  <input class="input input2" type="search" placeholder="Buscar...">
                  <button class="button button2" type="submit">
                  <h4>BUSCAR</h4>
                  </button>
                </form>
                
            </ul>
      </div>
    </nav>
   <!--Fin NavBar-->
  
  <!--Inicio cuerpo pagina -->
  <?php echo "Bienvenid@, " . $_SESSION['user_name'];?>
  <br>

  <div class="producto-info">
    <h3>Producto seleccionado para cotización</h3>
    <p><strong>Nombre:</strong> <?php echo $producto['Nombre_Producto']; ?></p>
    <p><strong>Descripción:</strong> <?php echo $producto['Descripcion_Producto']; ?></p>
    <p><strong>Precio:</strong> $<?php echo $producto['Precio_Producto']; ?></p>
    <p><strong>Vendedor:</strong> <?php echo $producto['Nombre_Usuario']; ?></p>
  </div>

  <!-- Botón para agregar contacto -->
  <button id="addContactButton" type="button" 
        data-user-id="<?php echo $_SESSION['user_id']; ?>" 
        data-seller-id="<?php echo $usuarioId; ?>">Agregar como contacto</button>


<script>
    
    //Agregar contacto al vendedor
  document.getElementById('addContactButton').addEventListener('click', (event) => {
    const userId = event.target.getAttribute('data-user-id');
    const sellerId = event.target.getAttribute('data-seller-id');
    console.log(`userId: ${userId}, sellerId: ${sellerId}`); // Verificar valores

    fetch('../Controladores/agregar_contacto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId, sellerId })
    }).then(response => response.json())
      .then(data => {
          console.log(data); // Verificar la respuesta del servidor
          if (data.success) {
              alert('Contacto agregado exitosamente');
          } else {
              alert('Error al agregar contacto');
          }
      }).catch(err => {
          console.error('Error:', err);
          alert('Error al procesar la solicitud.');
      });
});

</script>


</body>