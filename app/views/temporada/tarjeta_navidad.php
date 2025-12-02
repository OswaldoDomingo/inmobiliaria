<div id="app-navidad">
  <canvas id="canvas-navidad"></canvas>
  <div class="contenido-tarjeta">
      <h1>Bon <br> Nadal</h1>
      <button id="btn-cerrar-navidad">ENTRAR AL SITIO</button>
  </div>
</div>

<style>
/* He cambiado los IDs para que no choquen con tu web real */
#app-navidad {
  position: fixed; /* Fijo encima de todo */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99999; /* El número más alto para tapar todo */
  background-color: rgba(0,0,0,0.9); /* Fondo oscuro por si el canvas tarda en cargar */
  font-family: "Arial", serif;
  overflow: hidden;
}

#canvas-navidad {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
}

.contenido-tarjeta {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 100000;
    width: 100%;
}

#app-navidad h1 {
  font-size: 5rem; /* Ajustado para móviles */
  margin-bottom: 30px;
  font-family: "Arial", sans-serif;
  font-weight: bold;
  color: #fff;
  text-shadow: 0 0 5px #fff, 0 0 20px #EA4630;
  pointer-events: none; /* Para que los clicks pasen a través del texto */
}

/* Estilo del botón para cerrar */
#btn-cerrar-navidad {
    padding: 15px 30px;
    font-size: 18px;
    background: white;
    border: none;
    cursor: pointer;
    border-radius: 50px;
    font-weight: bold;
    color: #0630e7ff;
    box-shadow: 0 0 15px rgba(255,255,255,0.5);
    transition: transform 0.2s;
}

#btn-cerrar-navidad:hover {
    transform: scale(1.1);
    background-color: #0b63e7ff;
    color: white;
}

@media (min-width: 768px) {
    #app-navidad h1 { font-size: 9em; }
}
</style>

<script type="module">
  // Importamos la librería directamente
  import AttractionCursor from "https://cdn.jsdelivr.net/npm/threejs-components@0.0.26/build/cursors/attraction1.min.js"

  const contenedor = document.getElementById('app-navidad');
  const canvas = document.getElementById('canvas-navidad');
  const boton = document.getElementById('btn-cerrar-navidad');

  // Iniciamos la animación solo si existen los elementos
  if (contenedor && canvas) {
      const app = AttractionCursor(canvas, {
        particles: {
          attractionIntensity: 0.75,
          size: 0.5,  
        },
      });
  }

  // Lógica para cerrar la tarjeta al hacer click
  if(boton) {
      boton.addEventListener('click', () => {
          // Efecto de desvanecimiento
          contenedor.style.transition = "opacity 0.5s ease";
          contenedor.style.opacity = "0";
          
          // Esperar medio segundo y eliminar del HTML para que no moleste
          setTimeout(() => {
              contenedor.remove();
          }, 500);
      });
  }
</script>