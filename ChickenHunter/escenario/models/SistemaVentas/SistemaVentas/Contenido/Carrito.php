<?php
session_start();
$allowed_roles = ['Comprador']; // Solo el comprador puede entrar
if (!in_array($_SESSION['user_role'], $allowed_roles)) {
    header('Location: AccesoDenegado.php');
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

if (!$connection) {
    die("Error al conectar con la base de datos: " . mysqli_error($connection));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accion'])) {
        $userId = $_SESSION['user_id'];

        if ($data['accion'] === 'eliminar') {
            $productoId = $data['productoId'];
            $deleteQuery = "DELETE FROM carrito WHERE ID_Usuario = $userId AND ID_Producto = $productoId";
            if (mysqli_query($connection, $deleteQuery)) {
                echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
            }
            exit();
        } elseif ($data['accion'] === 'actualizar') {
            $productoId = $data['productoId'];
            $cantidad = $data['cantidad'];
            $updateQuery = "UPDATE carrito SET Cantidad = $cantidad WHERE ID_Usuario = $userId AND ID_Producto = $productoId";
            if (mysqli_query($connection, $updateQuery)) {
                echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad']);
            }
            exit();
        } elseif ($data['accion'] === 'comprar') {
            $query = "SELECT ID_Producto, Cantidad FROM carrito WHERE ID_Usuario = $userId";
            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $productoId = $row['ID_Producto'];
                    $cantidad = $row['Cantidad'];

                    $insertPedido = "INSERT INTO pedidos (ID_Usuario, ID_Producto, Cantidad) VALUES ($userId, $productoId, $cantidad)";
                    mysqli_query($connection, $insertPedido);
                }
                $deleteCarrito = "DELETE FROM carrito WHERE ID_Usuario = $userId";
                mysqli_query($connection, $deleteCarrito);

                echo json_encode(['success' => true, 'message' => 'Compra realizada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No hay productos en el carrito']);
            }
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
   <script src="https://www.paypal.com/sdk/js?client-id=ASS9Bb9zrNTpn0FHf3iHvTF12yKWbh9ZjlmwgKx1SF6CpI-adCyZ22GtePLzFrkr6CAexkpqHsUErwf6&currency=MXN"></script>

   <link rel="stylesheet" href="carrito.css">
   <title>Carrito de compra</title>
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

   <!--Contenido del carrito-->
   <h3>Carrito de compra</h3>
   <table id="tablaCarrito">
       <thead>
           <tr>
               <th>ID Producto</th>
               <th>Nombre</th>
               <th>Precio</th>
               <th>Cantidad</th>
               <th>Subtotal</th>
               <th>Acciones</th>
           </tr>
       </thead>
       <tbody>
           <?php
           $userId = $_SESSION['user_id'];
           $query = "SELECT c.ID_Producto, p.Nombre_Producto, p.Precio_Producto, c.Cantidad 
                     FROM carrito c 
                     INNER JOIN producto p ON c.ID_Producto = p.ID_Producto
                     WHERE c.ID_Usuario = $userId";
           $result = mysqli_query($connection, $query);
           $total = 0;

           if (mysqli_num_rows($result) > 0) {
               while ($row = mysqli_fetch_assoc($result)) {
                   $subtotal = $row['Precio_Producto'] * $row['Cantidad'];
                   $total += $subtotal;

                   echo "<tr>";
                   echo "<td>{$row['ID_Producto']}</td>";
                   echo "<td>{$row['Nombre_Producto']}</td>";
                   echo "<td>{$row['Precio_Producto']}</td>";
                   echo "<td><input type='number' min='1' value='{$row['Cantidad']}' 
                                 onchange='actualizarCantidad({$row['ID_Producto']}, this.value)'></td>";
                   echo "<td>$subtotal</td>";
                   echo "<td><button onclick='eliminarDelCarrito({$row['ID_Producto']})'>Eliminar</button></td>";
                   echo "</tr>";
               }
           } else {
               echo "<tr><td colspan='6'>No hay productos en el carrito</td></tr>";
           }
           ?>
       </tbody>
       <tfoot>
           <tr>
               <td colspan="4"><strong>Total</strong></td>
               <td colspan="2"><strong><?php echo $total; ?></strong></td>
           </tr>
       </tfoot>
   </table>

   <div id="paypal-button-container" style="display: flex; justify-content: center; align-items: center; margin-top: 20px;"></div>

      <!--Botón para realizar compra-->
      <button class="button" onclick="realizarCompra()">Realizar compra</button>

      <!--Botón para consultar compras-->
      <button class="button" onclick="location.href='consultar_compras.php';">Consultar compras</button>

   <script>
       function eliminarDelCarrito(productoId) {
           fetch('carrito.php', {
               method: 'POST',
               body: JSON.stringify({accion: 'eliminar', productoId: productoId}),
               headers: {'Content-Type': 'application/json'}
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   alert(data.message);
                   location.reload();
               } else {
                   alert(data.message);
               }
           });
       }

       function actualizarCantidad(productoId, cantidad) {
           fetch('carrito.php', {
               method: 'POST',
               body: JSON.stringify({accion: 'actualizar', productoId: productoId, cantidad: cantidad}),
               headers: {'Content-Type': 'application/json'}
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   location.reload();
               } else {
                   alert(data.message);
               }
           });
       }

       function realizarCompra() {
           fetch('carrito.php', {
               method: 'POST',
               body: JSON.stringify({accion: 'comprar'}),
               headers: {'Content-Type': 'application/json'}
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   alert(data.message);
                   location.reload();
               } else {
                   alert(data.message);
               }
           });
       }
   </script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: <?php echo $total; ?>  // Aquí se coloca el total calculado de la compra
                    },
                    description: 'Compra en FCFM Marketplace'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('¡Pago realizado con éxito!');

                // Realizar las acciones posteriores a la compra (guardar pedido en base de datos)
                fetch('carrito.php', {
                    method: 'POST',
                    body: JSON.stringify({accion: 'pagar'}),
                    headers: {'Content-Type': 'application/json'}
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();  // Recargar para actualizar el carrito
                    } else {
                        alert(data.message);
                    }
                });

            });
        },
        onCancel: function(data) {
            alert('El pago fue cancelado');
        }
    }).render('#paypal-button-container');  // Renderizar el botón de PayPal
</script>

</body>
</html>
