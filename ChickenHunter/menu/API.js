const pageID = '645367645318046';
    const accessToken = 'EAARBAPZCdR7UBO5VK3klCI25jPZBLOTq75Ts3tgo10E8ZBhlZCsy5w2V3aZBWU48q5bs3toFL5AZBH6BPa8MZCc5TYz2qC4asFBjZA7qph3Y3rYkXnxbiaS81ivGUwxUZAspIF1ZAdBeNtwH7oEz3FlZAVNlLsAhKM6JbcZCrCr8qLSn2E8sjkApOzTZCn0zZC5MsBzfGaZAz51AqEzhbdmlIFLK60IgUOH';

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