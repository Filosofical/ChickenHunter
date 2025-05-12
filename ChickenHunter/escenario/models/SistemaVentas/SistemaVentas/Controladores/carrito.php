<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Verificar si se recibió el ID del producto
if (isset($data['productoId'])) {
    $userId = $_SESSION['user_id']; // ID del usuario logueado
    $productoId = $data['productoId'];

    // Conexión a la base de datos
    $connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

    if (!$connection) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión']);
        exit();
    }

    // Verificar si el producto ya está en el carrito
    $query = "SELECT * FROM carrito WHERE ID_Usuario = $userId AND ID_Producto = $productoId";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'El producto ya está en tu carrito']);
    } else {
        // Agregar el producto al carrito
        $insertQuery = "INSERT INTO carrito (ID_Usuario, ID_Producto) VALUES ($userId, $productoId)";
        if (mysqli_query($connection, $insertQuery)) {
            echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar el producto']);
        }
    }

    if (isset($data['accion']) && $data['accion'] === 'eliminar') {
        $productoId = $data['productoId'];
        $userId = $_SESSION['user_id'];
    
        // Eliminar el producto del carrito
        $deleteQuery = "DELETE FROM carrito WHERE ID_Usuario = $userId AND ID_Producto = $productoId";
        if (mysqli_query($connection, $deleteQuery)) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
        }
        exit();
    }

    if (isset($data['accion']) && $data['accion'] === 'actualizar') {
        $productoId = $data['productoId'];
        $cantidad = $data['cantidad'];
        $userId = $_SESSION['user_id'];
    
        // Actualizar la cantidad del producto en el carrito
        $updateQuery = "UPDATE carrito SET Cantidad = $cantidad WHERE ID_Usuario = $userId AND ID_Producto = $productoId";
        if (mysqli_query($connection, $updateQuery)) {
            echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad']);
        }
        exit();
    }

    if (isset($data['accion']) && $data['accion'] === 'comprar') {
        $userId = $_SESSION['user_id'];
    
        // Obtener los productos del carrito
        $query = "SELECT ID_Producto, Cantidad FROM carrito WHERE ID_Usuario = $userId";
        $result = mysqli_query($connection, $query);
    
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $productoId = $row['ID_Producto'];
                $cantidad = $row['Cantidad'];
    
                // Registrar en una tabla de pedidos (crea esta tabla en tu BD)
                $insertPedido = "INSERT INTO pedidos (ID_Usuario, ID_Producto, Cantidad) VALUES ($userId, $productoId, $cantidad)";
                mysqli_query($connection, $insertPedido);
            }
    
            // Vaciar el carrito
            $deleteCarrito = "DELETE FROM carrito WHERE ID_Usuario = $userId";
            mysqli_query($connection, $deleteCarrito);
    
            echo json_encode(['success' => true, 'message' => 'Compra realizada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No hay productos en el carrito']);
        }
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'pagar') {
        $userId = $_SESSION['user_id'];
        // Registrar los pedidos en la base de datos
        $query = "INSERT INTO pedidos (ID_Usuario, ID_Producto, Cantidad, Total)
                  SELECT ID_Usuario, ID_Producto, Cantidad, $total FROM carrito WHERE ID_Usuario = $userId";
        mysqli_query($connection, $query);
    
        // Vaciar el carrito
        $deleteQuery = "DELETE FROM carrito WHERE ID_Usuario = $userId";
        mysqli_query($connection, $deleteQuery);
    
        echo json_encode(['success' => true, 'message' => 'Pedido registrado']);
        exit();
    }
    
    mysqli_close($connection);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no recibido']);
}
?>
