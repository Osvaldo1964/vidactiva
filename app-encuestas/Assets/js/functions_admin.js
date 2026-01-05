function controlTag(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8) return true;
    else if (tecla == 0 || tecla == 9) return true;
    patron = /[0-9\s]/;
    n = String.fromCharCode(tecla);
    return patron.test(n);
}

function testText(txtString) {
    var stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/);
    if (stringText.test(txtString)) {
        return true;
    } else {
        return false;
    }
}

function testAddress(address) {
    var stringAddress = new RegExp(/^[a-zA-Z0-9ÑñÁáÉéÍíÓóÚúÜü\s#.,-\/]+$/);
    return stringAddress.test(address);
}

function testEntero(intCant) {
    var intCantidad = new RegExp(/^([0-9])*$/);
    if (intCantidad.test(intCant)) {
        return true;
    } else {
        return false;
    }
}

function fntEmailValidate(email) {
    var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    if (stringEmail.test(email) == false) {
        return false;
    } else {
        return true;
    }
}

function fntValidText() {
    let validText = document.querySelectorAll(".validText");
    validText.forEach(function (validText) {
        validText.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!testText(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidNumber() {
    let validNumber = document.querySelectorAll(".validNumber");
    validNumber.forEach(function (validNumber) {
        validNumber.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!testEntero(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidEmail() {
    let validEmail = document.querySelectorAll(".validEmail");
    validEmail.forEach(function (validEmail) {
        validEmail.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!fntEmailValidate(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidAddress() {
    let validAddress = document.querySelectorAll(".validAddress");
    validAddress.forEach(function (input) {
        input.addEventListener('keyup', function () {
            let inputValue = this.value;
            // Si el campo está vacío, podrías decidir si es válido o no. 
            // Aquí validamos si cumple el patrón.
            if (!testAddress(inputValue) && inputValue !== "") {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

// ----------------------------------------------------------------------------------
// GLOBAL HELPERS (Optimización y Estandarización)
// ----------------------------------------------------------------------------------

const lenguajeEspanol = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
};

/**
 * Helper global para peticiones fetch con manejo de errores y token
 * @param {string} url - Endpoint completo
 * @param {string} method - GET, POST, PUT, DELETE
 * @param {object|FormData} body - Datos a enviar
 * @returns {Promise<object|null>} - Respuesta JSON o null
 */
async function fetchData(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('userToken')}`
        }
    };

    // Si no es FormData, asumimos JSON y añadimos Content-Type
    if (!(body instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
        if (body && method !== 'GET') options.body = JSON.stringify(body);
    } else {
        // Si es FormData, fetch pone el Content-Type automáticamente (multipart/form-data)
        if (body && method !== 'GET') options.body = body;
    }

    try {
        const response = await fetch(url, options);

        // Manejo básico de 401 (No autorizado)
        if (response.status === 401) {
            console.warn("Token expirado o inválido (401).");
            // swal("Sesión Expirada", "Tu sesión ha expirado, por favor inicia sesión nuevamente.", "warning");
            // window.location = BASE_URL + '/logout'; 
            return { status: false, msg: "Sesión expirada o no autorizada." };
        }

        const text = await response.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Respuesta válida no es JSON:", text);
            return { status: false, msg: "Error de servidor (respuesta no válida)" };
        }
    } catch (error) {
        console.error("Error de conexión:", error);
        return { status: false, msg: "Error de conexión con la API" };
    }
}

/**
 * Genera la configuración estándar para AJAX de DataTables con Token JWT
 * @param {string} url - Endpoint de la API
 * @param {object} additionalData - Datos extra para enviar (opcional)
 * @returns {object} Configuración ajax para DataTables
 */
function getDtAjaxOptions(url, additionalData = {}) {
    return {
        "url": url,
        "type": "GET",
        "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
        "data": function (d) {
            // Merge de datos estándar y adicionales
            return Object.assign(d, additionalData);
        },
        "dataSrc": function (json) {
            if (json.status && Array.isArray(json.data)) {
                return json.data;
            } else {
                // Si la sesión expiró o hay error 401 capturado por la API
                if (json.status === false && json.msg === "Token inválido o expirado") {
                    window.location = BASE_URL + '/logout/logout';
                }
                return [];
            }
        },
        "error": function (xhr, error, thrown) {
            if (xhr.status === 401) {
                swal("Sesión caducada", "Por favor inicie sesión nuevamente.", "warning");
                window.location = BASE_URL + '/logout/logout';
            } else {
                console.error("Error en DataTable:", xhr);
            }
        }
    };
}

// ----------------------------------------------------------------------------------

function checkAuth() {
    const token = localStorage.getItem('userToken');

    if (!token) {
        window.location.href = BASE_URL + "/login";
    }
}

function verificarExpiracionToken() {
    const token = localStorage.getItem('userToken');

    // Si no hay token, no hacemos nada (el middleware de PHP se encargará)
    if (!token) return;

    try {
        // Decodificamos el Payload del JWT
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const payload = JSON.parse(window.atob(base64));

        const tiempoActual = Math.floor(Date.now() / 1000);

        // Si el tiempo actual es mayor a la expiración
        if (payload.exp < tiempoActual) {
            swal({
                title: "Sesión Expirada",
                text: "Tu tiempo de acceso ha terminado. Por seguridad, debes ingresar nuevamente.",
                type: "warning",
                confirmButtonText: "Aceptar",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    // 1. Limpiamos el token localmente de inmediato
                    localStorage.removeItem('userToken');

                    // 2. Redirigimos al logout del servidor
                    window.location.href = BASE_URL + '/logout/logout';
                }
            });
        }
    } catch (e) {
        console.error("Error al decodificar el token:", e);
    }
}

window.addEventListener('load', function () {
    fntValidText();
    fntValidEmail();
    fntValidNumber();

    // Validar Rol para botón de inicialización
    if (localStorage.getItem('userRol') == 1 && document.querySelector('#liInitEscrutinio')) {
        document.querySelector('#liInitEscrutinio').classList.remove('d-none');
    }
}, false);
