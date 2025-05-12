<?php
// Iniciamos la sesión
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="chat.css">
    <title>Chat Privado</title>
</head>
<body>
    <!--Inicio NavBar-->
    <nav class="navbar">
        <div class="container">
            <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                <!--Inicio-->
                <a class="navbar-brand" href="Inicio.php"><h4>FCFM Marketplace</h4></a>
                <!--Productos-->
                <a class="nav-link" href="Productos.php"><h4>PRODUCTOS</h4></a>
                <!--Categorías-->
                <a class="nav-link" href="Categorias.php"><h4>CATEGORIAS</h4></a>
                <!--Perfil-->
                <a class="nav-link" href="PerfilUser.php"><h4>PERFIL</h4></a>
                <!--Listas-->
                <a class="nav-link" href="Listas.php"><h4>LISTAS</h4></a>
                <!--Carrito-->
                <a class="nav-link" href="Carrito.php"><h4>CARRITO</h4></a>
                <!--Chat-->
                <a class="nav-link" href="ChatPrivado.php"><h4>CHAT</h4></a>
                <!--Cerrar sesión-->
                <a class="nav-link" href="../Login/Login.php"><h4>SALIR</h4></a>
                <!--Buscar-->
                <form class="d-flex form2">
                    <input class="input input2" type="search" placeholder="Buscar...">
                    <button class="button button2" type="submit"><h4>BUSCAR</h4></button>
                </form>
            </ul>
        </div>
    </nav>
    <!--Fin NavBar-->

    <!--Cuerpo de la página-->
    <h2>Bienvenid@, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

    <div id="contactos" class="contact-list">
        <!-- Lista de contactos cargada dinámicamente -->
    </div>

    <div id="chat-container" class="chat-container">
        <div class="chat-messages" id="chatMessages"></div>

        <div class="chat-input">
            <input id="messageInput" type="text" placeholder="Escribe un mensaje...">
            <button id="sendButton" type="button">Enviar</button>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const chatMessages = document.getElementById('chatMessages');
    const contactos = document.getElementById('contactos');

    const userId = <?php echo $_SESSION['user_id']; ?>;
    const sellerId = <?php echo $usuarioId; ?>; // Asumiendo que esta variable es pasada a la página

    // Cargar mensajes antiguos
// Esta función se debe ejecutar para cargar los mensajes cuando la página se carga o se envíe un nuevo mensaje
function cargarMensajes() {
    var idProducto = /* El ID del producto que se está cotizando */;
    var idVendedor = /* El ID del vendedor correspondiente */;

    fetch('../Controladores/obtener_mensajes.php?id_producto=' + idProducto + '&id_vendedor=' + idVendedor)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var chatMessages = document.getElementById('chatMessages');
                chatMessages.innerHTML = ''; // Limpiar los mensajes previos
                data.mensajes.forEach(mensaje => {
                    var mensajeHtml = '<p><strong>' + mensaje.Nombre_Usuario + ': </strong>' + mensaje.Mensaje + '</p>';
                    chatMessages.innerHTML += mensajeHtml;
                });
            } else {
                console.log(data.message);
            }
        }).catch(err => {
            console.error('Error:', err);
        });
}

// Llamar a la función al cargar la página
window.onload = cargarMensajes;


    // Enviar un mensaje
    document.getElementById('sendButton').addEventListener('click', function() {
    var mensaje = document.getElementById('messageInput').value;
    if (mensaje.trim() === "") {
        alert("El mensaje no puede estar vacío.");
        return;
    }

    var idProducto = /* El ID del producto que se está cotizando */;
    var idVendedor = /* El ID del vendedor correspondiente */;
    
    // Enviar el mensaje a través de AJAX
    var formData = new FormData();
    formData.append('mensaje', mensaje);
    formData.append('id_producto', idProducto);
    formData.append('id_vendedor', idVendedor);

    fetch('enviar_mensaje.php', {
        method: 'POST',  // Asegúrate de que el método sea POST
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log(data.message);
              document.getElementById('chatMessages').innerHTML += '<p><strong>Tú: </strong>' + mensaje + '</p>';
              document.getElementById('messageInput').value = ''; // Limpiar el campo de texto
          } else {
              alert(data.message);
          }
      }).catch(err => {
          console.error('Error:', err);
          alert('Error al enviar el mensaje');
      });
});



    // Cargar la lista de contactos
    function loadContacts() {
        fetch('../Controladores/cargar_contactos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  contactos.innerHTML = ''; // Limpiar contactos
                  data.contacts.forEach(contact => {
                      const contactElement = document.createElement('div');
                      contactElement.classList.add('contact');
                      contactElement.innerHTML = `<strong>${contact.Nombre_Usuario}</strong>`;
                      contactos.appendChild(contactElement);
                  });
              }
          }).catch(err => console.error(err));
    }

    loadMessages(); // Cargar mensajes al inicio
    loadContacts(); // Cargar contactos

    // Actualizar los mensajes cada 5 segundos
    setInterval(loadMessages, 5000);
});
</script>

</body>
</html>
