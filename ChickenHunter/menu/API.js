const pageID = '645367645318046';
    const accessToken = 'EAARBAPZCdR7UBOzFr6ZBW0aZCVLo5bSebjsxCJiZBHatEZBFZCbC8EuOZBXa6Unk2bFNIS5CE3ZAyMtzn8TvZBULn0YZAO7K9ADHZBIxFTHs7f9ZCShNTBlCBnzgzmStq6esMDZC4G7i3tJbBvcgLu0u2458QjMbGd4MVVP4KTAVeE4k6a5AzQL93u2Yi5HlBhNXiIJkqcBFHKX6xgXiM9SyO0ijBcHqT';

    window.fbAsyncInit = function () {
      FB.init({
        appId: '1197372455339957',
        xfbml: true,
        version: 'v22.0',
      });

      obtenerPublicaciones(); // Mostrar publicaciones al cargar
    };

    function publicarEnFacebook() {
      const mensaje = document.getElementById('postMessage').value;

      if (mensaje.trim() === '') {
        alert('Escribe algo para publicar.');
        return;
      }

      FB.api(
        `/${pageID}/feed`,
        'POST',
        {
          message: mensaje,
          access_token: accessToken,
        },
        function (response) {
          if (response && !response.error) {
            alert('Publicado correctamente');
            document.getElementById('postMessage').value = '';
            obtenerPublicaciones();
          } else {
            console.error(response.error);
            alert('Error al publicar');
          }
        }
      );
    }

    function obtenerPublicaciones() {
      FB.api(
        `/${pageID}/feed`,
        'GET',
        {
          access_token: accessToken,
        },
        function (response) {
          const container = document.getElementById('feedContainer');
          container.innerHTML = ''; // Limpiar

          if (response && !response.error) {
            response.data.slice(0, 5).forEach(post => {
              const div = document.createElement('div');
              div.style.border = '1px solid #ccc';
              div.style.padding = '10px';
              div.style.margin = '10px 0';
              div.innerHTML = post.message ? post.message : '[Publicación sin texto]';
              container.appendChild(div);
            });
          } else {
            container.innerHTML = 'No se pudieron cargar las publicaciones.';
            console.error(response.error);
          }
        }
      );
    }