<?php
session_start();
$allowed_roles = ['Administrador', 'Vendedor']; // Lista de roles permitidos
if (!in_array($_SESSION['user_role'], $allowed_roles)) {
    header('Location: AccesoDenegado.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Categorias.css">
    <title>Categorías</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
   
   <!--Inicio cuerpo pagina landing-->
   <?php echo "Bienvenid@, " . $_SESSION['user_name'];?> 

   <!-- Registro de categorías -->
   <h2>Crear categoría</h2>
    <div class="contenedor-form registrar">
        <form id="categoriaForm">
            <div class="contenedor-input">
                <span class="icono"><i class="fa-solid fa-signature"></i></span>
                <input type="text" name="nombre_categoria" id="nombre_categoria" required>
                <label for="nombre_categoria">Nombre de la categoría</label>
            </div>
            <div class="contenedor-input">
                <span class="icono"><i class="fa-solid fa-book"></i></span>
                <input type="text" name="descripcion_categoria" id="descripcion_categoria" required>
                <label for="descripcion_categoria">Descripción</label>
            </div>
            <br>
            <button type="submit" class="button">Publicar categoría</button>
        </form>
    </div>

    <!-- Tabla de categorías -->
    <h2>Lista de categorías</h2>
    <div id="tablaCategorias">
    </div>

    <script>
        $(document).ready(function() {
            // Función para cargar categorías
            function cargarCategorias() {
                $.ajax({
                    url: '../Controladores/categoria.php',
                    method: 'GET',
                    success: function(response) {
                        $('#tablaCategorias').html(response);
                    },
                    error: function() {
                        alert('Error al cargar las categorías');
                    }
                });
            }
            cargarCategorias();

            $('#categoriaForm').submit(function(event) {
                event.preventDefault(); // Con esto evitamos recargar la página
                const nombre = $('#nombre_categoria').val();
                const descripcion = $('#descripcion_categoria').val();

                $.ajax({
                    url: '../Controladores/categoria.php',
                    method: 'POST',
                    data: { nombre_categoria: nombre, descripcion_categoria: descripcion },
                    success: function(response) {
                        alert('Categoría registrada correctamente');
                        $('#categoriaForm')[0].reset(); 
                        cargarCategorias(); // Recargar categorías
                    },
                    error: function() {
                        alert('Error al registrar la categoría');
                    }
                });
            });
        });
    </script>

     <!-- Registro de productos-->
   <h2>Publicar productos</h2>
    <div class="contenedor-form registrar">
        <!-- Definimos a que ruta y por medio de que método envíaremos la información del registro -->
         <form action="../Controladores/producto.php" method="post" enctype="multipart/form-data" onsubmit="mostrarMensaje()">

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-signature"></i></span>
            <input type="text" name="nombre_producto" required>
            <br>
            <label for="nombre_producto">Nombre del producto</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-book"></i></span>
            <input type="text" name="descripción_producto" required>
            <br>
            <label for="descripción_producto">Descripción</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-list"></i></span>
            <select name="categoria_producto" id="categoria_producto" required>
                <?php
                // Conexión a la base de datos
                $host = "localhost";
                $user = "root";
                $pass = "";
                $connection = mysqli_connect($host, $user, $pass, "sistemadeventas");

                if(!$connection) {
                    die("No se ha podido conectar con el servidor: " . mysqli_error($connection));
                }

                $categoriaQuery = "SELECT ID_Categoria, Nombre_Categoria FROM categoria";
                $categoriaResult = mysqli_query($connection, $categoriaQuery);

                if (mysqli_num_rows($categoriaResult) > 0) {
                    while ($row = mysqli_fetch_assoc($categoriaResult)) {
                        echo "<option value='{$row['ID_Categoria']}'>{$row['Nombre_Categoria']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay categorías disponibles</option>";
                }
                ?>
            </select>
            <br>
            <label for="categoria_producto">Categoría</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-cash-register"></i></span>
            <select name="cotizar_vender" id="cotizar_vender" required>
            <option value="Cotizar">Cotizar</option>
            <option value="Vender">Vender</option>
            </select>
            <br>
            <label for="rol_usuario">Tipo de venta</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-dollar-sign"></i></span>
            <input type="text" name="precio_producto" required>
            <br>
            <label for="precio_producto">Precio</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-boxes-stacked"></i></span>
            <input type="text" name="cantidad_producto" required>
            <br>
            <label for="cantidad_producto">Cantidad disponible</label>
        </div>

        <div class="contenedor-input">
             <span class="icono"><i class="fa-solid fa-camera"></i></span>
             <label for="imgRutaProducto">Foto del producto:</label>  
             <img id="imgProducto" src="#" alt="Vista previa de la imagen" style="display: none; width: 100px;">
             <br>
             <input class="inputImgProducto" type="file" id="imgRutaProducto" name="imgRutaProducto" accept="image/*" onchange="previewImage()">
        </div>

         <!-- Campo para subir el video del producto -->
        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-video"></i></span>
            <label for="videoRutaProducto">Video del producto:</label>
            <br>
            <input type="file" id="videoRutaProducto" name="videoRutaProducto" accept="video/*">
        </div>

        <br>
        <button type="submit" class="button">Publicar producto</button>

        </form>
    </div>

    <script>
    function mostrarMensaje() {
        alert("Su producto está en espera de ser aprobado por un administrador");
    }
    </script>

    <script src="PerfilUser.js"></script> 
    <script src="PerfilUserValidacion.js"></script> 
</body>
</html>