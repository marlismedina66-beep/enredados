import os

content = """/* ============================================================
   Enredados Wiki - script de la aplicación con Base de Datos
   Funcionalidades:
   1. Carga de personajes desde la base de datos MySQL (API PHP).
   2. Cuadro modal dinámico (ficha de personaje / detalle de imagen).
   3. Formulario de captura de datos con validación y creación de tarjeta,
      incluyendo campos de ROPA Y ACCESORIOS (cabello, camiseta, pantalón, zapatos).
   4. Envío de datos al servidor via AJAX (fetch) y persistencia en MySQL.

   El framework Bootstrap 5.3 gestiona la apertura y el cierre del modal
   y el comportamiento del alerta mediante sus atributos data-bs.
   ============================================================ */

(function () {
  'use strict';

  // Configuración de la API
  var API_BASE = 'http://localhost:8899/api';
  var POSTER = 'https://static.wikia.nocookie.net/disney/images/7/71/Enredados-cartel.jpg/revision/latest?cb=20110309115722&path-prefix=es';

  // Elementos del DOM
  var gridPersonajes = document.getElementById('gridPersonajes');
  var formulario = document.getElementById('formularioPersonaje');
  var alertaRegistro = document.getElementById('alertaRegistro');
  var mensajeRegistro = document.getElementById('mensajeRegistro');

  // ===== FUNCIONES AUXILIARES =====

  function obtenerElemento(id) {
    return document.getElementById(id);
  }

  // Muestra un mensaje de error en la alerta de registro
  function mostrarError(mensaje) {
    alertaRegistro.className = 'alert alert-danger mt-3';
    alertaRegistro.hidden = false;
    mensajeRegistro.textContent = mensaje;
  }

  // Muestra un mensaje de éxito en la alerta de registro
  function mostrarExito(mensaje) {
    alertaRegistro.className = 'alert alert-success mt-3';
    alertaRegistro.hidden = false;
    mensajeRegistro.textContent = mensaje;
  }

  // ===== FUNCIONES DE API =====

  // Obtiene todos los personajes de la base de datos
  function cargarPersonajesDesdeAPI() {
    return fetch(API_BASE + '/leer.php')
      .then(function (respuesta) {
        if (!respuesta.ok) {
          throw new Error('Error HTTP: ' + respuesta.status);
        }
        return respuesta.json();
      })
      .then(function (data) {
        if (data.success) {
          return data.personajes || [];
        } else {
          throw new Error(data.error || 'Error al cargar personajes');
        }
      })
      .catch(function (error) {
        console.error('Error cargando personajes:', error);
        return [];
      });
  }

  // Envía un personaje a la base de datos (crear o actualizar)
  function guardarPersonajeEnAPI(datos) {
    return fetch(API_BASE + '/guardar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(datos)
    })
    .then(function (respuesta) {
      if (!respuesta.ok) {
        throw new Error('Error HTTP: ' + respuesta.status);
      }
      return respuesta.json();
    })
    .then(function (data) {
      if (data.success) {
        mostrarExito(data.mensaje || 'Personaje registrado correctamente');
        return true;
      } else {
        throw new Error(data.error || 'Error al guardar');
      }
    })
    .catch(function (error) {
      console.error('Error guardando personaje:', error);
      mostrarError('Error al guardar el personaje: ' + error.message);
      return false;
    });
  }

  // Elimina un personaje de la base de datos
  function eliminarPersonajeDeAPI(id) {
    return fetch(API_BASE + '/eliminar.php', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id: id })
    })
    .then(function (respuesta) {
      if (!respuesta.ok) {
        throw new Error('Error HTTP: ' + respuesta.status);
      }
      return respuesta.json();
    })
    .then(function (data) {
      if (data.success) {
        return true;
      } else {
        throw new Error(data.error || 'Error al eliminar');
      }
    })
    .catch(function (error) {
      console.error('Error eliminando personaje:', error);
      mostrarError('Error al eliminar: ' + error.message);
      return false;
    });
  }

  // ===== FUNCIONES DE RENDERIZADO =====

  // Construye el artículo de tarjeta a partir de los datos del personaje
  function crearTarjetaPersonaje(datos) {
    var tarjeta = document.createElement('article');
    tarjeta.className = 'tarjeta-personaje h-100';

    var imagen = datos.imagen || POSTER;

    var imagenElemento = document.createElement('img');
    imagenElemento.className = 'tarjeta-imagen';
    imagenElemento.src = imagen;
    imagenElemento.alt = datos.alternativa || datos.nombre;

    var cuerpo = document.createElement('div');
    cuerpo.className = 'tarjeta-cuerpo';

    var encabezado = document.createElement('div');
    encabezado.className = 'tarjeta-encabezado';

    var titulo = document.createElement('h3');
    titulo.className = 'tarjeta-titulo';
    titulo.textContent = datos.nombre;

    var badge = document.createElement('span');
    badge.className = 'badge ' + claseBadge(datos.rol);
    badge.textContent = datos.rol || 'Personaje';

    encabezado.appendChild(titulo);
    encabezado.appendChild(badge);

    var texto = document.createElement('p');
    texto.className = 'tarjeta-texto';
    texto.textContent = datos.descripcion || 'Sin descripción registrada.';

    // Sección de ropa y accesorios
    var seccionRopa = document.createElement('div');
    seccionRopa.className = 'ropa-seccion';
    seccionRopa.setAttribute('role', 'list');
    seccionRopa.setAttribute('aria-label', 'Ropa y accesorios de ' + (datos.nombre || 'personaje'));

    var agregarRopa = function (etiqueta, valor) {
      if (valor && valor.trim()) {
        var item = document.createElement('span');
        item.textContent = etiqueta + ': ' + valor;
        seccionRopa.appendChild(item);
      }
    };

    agregarRopa('Cabello', datos.ropa_cabello);
    agregarRopa('Camiseta', datos.ropa_camiseta);
    agregarRopa('Pantalon', datos.ropa_pantalon);
    agregarRopa('Zapatos', datos.ropa_zapatos);

    // Si no hay ropa especificada
    if (!seccionRopa.textContent.trim()) {
      var sinRopa = document.createElement('span');
      sinRopa.textContent = 'Sin ropa especificada';
      sinRopa.style.color = 'var(--color-texto-suave)';
      sinRopa.style.fontStyle = 'italic';
      seccionRopa.appendChild(sinRopa);
    }

    // Botón de ficha
    var boton = document.createElement('button');
    boton.type = 'button';
    boton.className = 'btn btn-primary w-100';
    boton.setAttribute('data-bs-toggle', 'modal');
    boton.setAttribute('data-bs-target', '#modalDetalle');
    boton.setAttribute('data-bs-etiqueta', 'Ficha');
    boton.setAttribute('data-bs-titulo', datos.nombre);
    boton.setAttribute('data-bs-subtitulo', datos.rol || 'Personaje');
    boton.setAttribute('data-bs-badge', datos.rol || '');
    boton.setAttribute('data-bs-descripcion', datos.descripcion || 'Sin descripción registrada.');
    boton.setAttribute('data-bs-imagen', imagen);
    boton.setAttribute('data-bs-alt', datos.alternativa || datos.nombre);
    boton.setAttribute('data-bs-id', datos.id || '');
    boton.textContent = 'Ver ficha';

    // Opción de eliminar
    var botonEliminar = document.createElement('button');
    botonEliminar.type = 'button';
    botonEliminar.className = 'btn btn-outline-danger w-100';
    botonEliminar.setAttribute('data-bs-toggle', 'modal');
    botonEliminar.setAttribute('data-bs-target', '#confirmarEliminar');
    botonEliminar.setAttribute('data-bs-id', datos.id || '');
    botonEliminar.textContent = 'Eliminar';

    cuerpo.appendChild(encabezado);
    cuerpo.appendChild(texto);
    cuerpo.appendChild(seccionRopa);
    cuerpo.appendChild(boton);
    cuerpo.appendChild(botonEliminar);

    tarjeta.appendChild(imagenElemento);
    tarjeta.appendChild(cuerpo);

    return tarjeta;
  }

  // ===== CONFIGURACIÓN DEL FORMULARIO =====

  function configurarFormulario() {
    formulario.addEventListener('submit', function (evento) {
      evento.preventDefault();

      if (!this.checkValidity()) {
        this.classList.add('was-validated');
        var primerInvalido = this.querySelector(':invalid');
        if (primerInvalido) { primerInvalido.focus(); }
        return;
      }

      this.classList.remove('was-validated');

      // Recoger datos del formulario
      var datos = new FormData(this);
      var nombre = (datos.get('nombre') || '').trim();
      var rol = (datos.get('rol') || '').trim();
      var aldea = (datos.get('aldea') || '').trim();
      var poder = datos.get('poder') || '50';
      var imagen = (datos.get('imagen') || '').trim() || POSTER;
      var descripcion = (datos.get('descripcion') || '').trim();
      var ropa_cabello = (datos.get('ropa_cabello') || '').trim();
      var ropa_camiseta = (datos.get('ropa_camiseta') || '').trim();
      var ropa_pantalon = (datos.get('ropa_pantalon') || '').trim();
      var ropa_zapatos = (datos.get('ropa_zapatos') || '').trim();

      // Datos a enviar a la API
      var payload = {
        nombre: nombre,
        rol: rol,
        aldea: aldea,
        poder: poder,
        imagen: imagen,
        descripcion: descripcion,
        ropa_cabello: ropa_cabello,
        ropa_camiseta: ropa_camiseta,
        ropa_pantalon: ropa_pantalon,
        ropa_zapatos: ropa_zapatos
      };

      // Guardar en la API
      guardarPersonajeEnAPI(payload)
        .then(function (exito) {
          if (exito) {
            // Recargar los personajes de la API
            cargarPersonajesDesdeAPI()
              .then(function (personajes) {
                gridPersonajes.innerHTML = '';
                personajes.forEach(function (personaje) {
                  gridPersonajes.appendChild(crearTarjetaPersonaje(personaje));
                });
              })
              .catch(function (error) {
                console.error('Error cargando personajes:', error);
              });
          }
          formulario.reset();
          alertaRegistro.hidden = true;
        });
    });
  }

  // ===== MONTAJE INICIAL =====

  document.addEventListener('DOMContentLoaded', function () {
    configurarModal();
    configurarFormulario();

    // Cargar personajes existentes al inicio
    cargarPersonajesDesdeAPI()
      .then(function (personajes) {
        gridPersonajes.innerHTML = '';
        personajes.forEach(function (personaje) {
          gridPersonajes.appendChild(crearTarjetaPersonaje(personaje));
        });
      })
      .catch(function (error) {
        console.error('Error al cargar personajes:', error);
        mostrarError('No se pudieron cargar los personajes: ' + error.message);
      });
  });
})();
"""

with open('/c/xampp/htdocs/enredados/app.js', 'w') as f:
    f.write(content)

print('Written successfully')
print('Size:', len(content), 'bytes')
