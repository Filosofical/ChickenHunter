//Función para mostrar vista previa de la imagen de perfil
function previewImage() {
    const file = document.getElementById('imgRutaProducto').files[0];
     const preview = document.getElementById('imgProducto');

     const reader = new FileReader();
     reader.onloadend = function() {
         preview.src = reader.result;
         preview.style.display = "block";
     };
     if (file) {
         reader.readAsDataURL(file);
     } else {
         preview.src = "";
         preview.style.display = "none";
     }
 }