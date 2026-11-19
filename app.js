/**
 * Enredados Wiki — Aplicación academica
 * Carga de personajes, galería, registro e inicio de sesión.
 */

(function () {
  'use strict';

  // ====== Configuración de la API ======
  // La API está en la misma raíz del servidor (Apache + PHP)
  var API_BASE = 'http://localhost/enredados/api';

  // Elementos del DOM
  var gridPersonajes = document.getElementById('gridPersonajes');
  var btnCargarMas = document.getElementById('btnCargarMas');
  var loginForm = document.getElementById('loginForm');
  var registerForm = document.getElementById('registerForm');
  var loginError = document.getElementById('loginError');
  var registerError = document.getElementById('registerError');
  var loginEmail = document.getElementById('loginEmail');
  var loginPassword = document.getElementById('loginPassword');
  var regName = document.getElementById('regName');
  var regEmail = document.getElementById('regEmail');
  var regPassword = document.getElementById('regPassword');
  var contactTabs = document.querySelectorAll('.contact-tab');
  var contactPanels = document.querySelectorAll('.contact-panel');

  var AUTH_KEY = 'enredados_wiki_user';

  // ====== Helpers ======
  function showError(el, msg) {
    el.textContent = msg || '';
  }

  function clearError(el) {
    el.textContent = '';
  }

  function setAuth(user) {
    try {
      localStorage.setItem(AUTH_KEY, JSON.stringify(user));
    } catch (e) {
      console.warn('No se pudo guardar la sesión', e);
    }
  }

  function clearAuth() {
    try {
      localStorage.removeItem(AUTH_KEY);
    } catch (e) {
      console.warn('No se pudo borrar la sesión', e);
    }
  }

  function getAuth() {
    try {
      var raw = localStorage.getItem(AUTH_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function showAuthUser() {
    var user = getAuth();
    if (user) {
      console.log('Usuario autenticado:', user);
      return true;
    }
    return false;
  }

  // ====== API: Personajes ======
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
        showError(registerError, data.mensaje || 'Personaje registrado correctamente');
        return true;
      } else {
        throw new Error(data.error || 'Error al guardar');
      }
    })
    .catch(function (error) {
      console.error('Error guardando personaje:', error);
      showError(registerError, 'Error al guardar el personaje: ' + error.message);
      return false;
    });
  }

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
      showError(registerError, 'Error al eliminar: ' + error.message);
      return false;
    });
  }

  // ====== Renderizado ======
  function crearTarjetaPersonaje(datos) {
    var tarjeta = document.createElement('article');
    tarjeta.className = 'personaje-card';

    var imagen = datos.imagen || 'imagenes/hero_poster.jpg';

    var imgElement = document.createElement('img');
    imgElement.className = 'personaje-img';
    imgElement.src = imagen;
    imgElement.alt = datos.alternativa || datos.nombre || 'Personaje';
    // Fallback: si la imagen no carga, mostrar un placeholder
    imgElement.addEventListener('error', function () {
      imgElement.style.display = 'none';
      var placeholder = tarjeta.querySelector('.personaje-placeholder');
      if (placeholder) {
        placeholder.style.display = 'flex';
        placeholder.querySelector('span').textContent = 'Imagen no disponible';
      }
    }, true);

    var cuerpo = document.createElement('div');
    cuerpo.className = 'personaje-body';

    var encabezado = document.createElement('h3');
    encabezado.className = 'personaje-name';
    encabezado.textContent = datos.nombre || 'Sin nombre';

    var badge = document.createElement('span');
    badge.className = 'personaje-rol';
    badge.textContent = datos.rol || 'Personaje';

    var texto = document.createElement('p');
    texto.className = 'personaje-desc';
    texto.textContent = datos.descripcion || 'Sin descripción registrada.';

    encabezado.appendChild(badge);

    cuerpo.appendChild(encabezado);
    cuerpo.appendChild(texto);

    tarjeta.appendChild(imgElement);
    tarjeta.appendChild(cuerpo);

    return tarjeta;
  }

  function renderizarPersonajes(personajes) {
    gridPersonajes.innerHTML = '';
    if (personajes.length === 0) {
      var placeholder = document.createElement('div');
      placeholder.className = 'personaje-card placeholder-card';
      placeholder.innerHTML = '<div class="personaje-placeholder"><i class="bi bi-image"></i><span>No hay personajes registrados</span></div>';
      gridPersonajes.appendChild(placeholder);
      return;
    }
    personajes.forEach(function (personaje) {
      gridPersonajes.appendChild(crearTarjetaPersonaje(personaje));
    });
  }

  // ====== Carga inicial ======
  function cargarPersonajes() {
    cargarPersonajesDesdeAPI()
      .then(function (personajes) {
        renderizarPersonajes(personajes);
      })
      .catch(function (error) {
        console.error('Error:', error);
        var placeholder = document.createElement('div');
        placeholder.className = 'personaje-card placeholder-card';
        placeholder.innerHTML = '<div class="personaje-placeholder"><i class="bi bi-exclamation-circle"></i><span>No se pudieron cargar los personajes</span></div>';
        gridPersonajes.innerHTML = '';
        gridPersonajes.appendChild(placeholder);
      });
  }

  // ====== Boton "Cargar más" ======
  btnCargarMas.addEventListener('click', function () {
    // En esta demo, el "cargar más" refleja la data real (ya cargada).
    // Podrías extender el query de la API para paginación.
    if (gridPersonajes.querySelector('.placeholder-card')) {
      cargarPersonajes();
    }
  });

  // ====== Autenticación ======
  function handleLogin(e) {
    e.preventDefault();
    clearError(loginError);
    loginForm.classList.remove('was-validated');

    var email = loginEmail.value.trim();
    var password = loginPassword.value.trim();

    if (!email) {
      showError(loginError, 'Ingresa tu correo electrónico.');
      loginEmail.focus();
      return;
    }
    if (!password) {
      showError(loginError, 'Ingresa tu contraseña.');
      loginPassword.focus();
      return;
    }

    var formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch(API_BASE + '/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        clearError(loginError);
        setAuth(data.usuario);
        loginForm.reset();
        showError(loginError, 'Hola, ' + data.usuario.nombre + '!');
      } else {
        showError(loginError, data.error || 'Error al iniciar sesión.');
      }
    })
    .catch(function (err) {
      console.error(err);
      showError(loginError, 'No se pudo conectar con el servidor.');
    });
  }

  function handleRegister(e) {
    e.preventDefault();
    clearError(registerError);
    registerForm.classList.remove('was-validated');

    var name = regName.value.trim();
    var email = regEmail.value.trim();
    var password = regPassword.value.trim();

    if (!name) {
      showError(registerError, 'Ingresa tu nombre.');
      regName.focus();
      return;
    }
    if (!email) {
      showError(registerError, 'Ingresa tu correo electrónico.');
      regEmail.focus();
      return;
    }
    if (!password || password.length < 6) {
      showError(registerError, 'La contraseña debe tener al menos 6 caracteres.');
      regPassword.focus();
      return;
    }

    var formData = new FormData();
    formData.append('nombre', name);
    formData.append('email', email);
    formData.append('password', password);

    fetch(API_BASE + '/register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        clearError(registerError);
        registerForm.reset();
        showError(registerError, 'Cuenta creada. Ahora puedes iniciar sesión.');
        // Volver al login
        document.querySelector('.contact-tab[data-panel="loginPanel"]').click();
      } else {
        showError(registerError, data.error || 'Error al registrar.');
      }
    })
    .catch(function (err) {
      console.error(err);
      showError(registerError, 'No se pudo conectar con el servidor.');
    });
  }

  // ====== Tabs de contacto ======
  contactTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var panelId = tab.getAttribute('data-panel');
      contactPanels.forEach(function (panel) {
        panel.classList.remove('active');
        if (panel.id === panelId) {
          panel.classList.add('active');
        }
      });
      contactTabs.forEach(function (t) {
        t.className = 'contact-tab';
      });
      tab.className = 'contact-tab contact-tab-active';
    });
  });

  // ====== Inicialización ======
  function init() {
    // Cargar personajes al inicio
    cargarPersonajes();

    // Event listeners de formularios
    if (loginForm) {
      loginForm.addEventListener('submit', handleLogin);
    }
    if (registerForm) {
      registerForm.addEventListener('submit', handleRegister);
    }

    // Validación de formularios con Bootstrap
    var forms = document.querySelectorAll('.contact-form');
    if (forms.length === 0) {
      // Si no existen .contact-form, validar manualmente
      if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
          e.preventDefault();
          var valid = loginEmail.checkValidity() && loginPassword.checkValidity();
          if (!valid) {
            loginForm.classList.add('was-validated');
            showError(loginError, 'Completa todos los campos obligatorios.');
          } else {
            handleLogin(e);
          }
        });
      }
      if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
          e.preventDefault();
          var valid = regName.checkValidity() && regEmail.checkValidity() && regPassword.checkValidity();
          if (!valid) {
            registerForm.classList.add('was-validated');
            showError(registerError, 'Completa todos los campos obligatorios.');
          } else {
            handleRegister(e);
          }
        });
      }
    }
  }

  document.addEventListener('DOMContentLoaded', init);
})();
