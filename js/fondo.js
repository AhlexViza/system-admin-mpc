    // Array con las rutas de las imágenes que quieres usar
    const imagenes = [
        'img/canchis-sicuani.jpg',
        'img/estadio.jpg',
        'img/sicuani.jpeg',
        'img/parque.png'
      ];
  
      // Función que elige una imagen aleatoria
      function cambiarFondo() {
        const indiceAleatorio = Math.floor(Math.random() * imagenes.length); // Elige un índice aleatorio
        document.querySelector('.fondo-personalizado').style.backgroundImage = `url(${imagenes[indiceAleatorio]})`; // Cambia el fondo
      }
  
      // Llamamos a la función para cambiar el fondo cuando se cargue la página
      window.onload = cambiarFondo;