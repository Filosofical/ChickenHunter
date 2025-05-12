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
    <link rel="stylesheet" href="Inicio.css">
    <title>Inicio</title>
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
   
   <!--Inicio cuerpo pagina landing-->
   <?php echo "Bienvenid@, " . $_SESSION['user_name'];?> 

     <!--Contenido-->
     <div class="contenido">
    <div class="tituloBox">
        <h2>Bienvenid@<br></h2>
        <h2>al <span>mercado de FCFM</span></h2>
        <p>encuentra todo lo que necesitas</p>
    </div>    
   </div>

 </body>
</html> 
