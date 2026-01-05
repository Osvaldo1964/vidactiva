// functions_encuestas.js
// Optimizado para arquitectura API con JWT y Async/Await

let tableEncuestas;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicialización de DataTables
    tableEncuestas = $('#tableEncuestas').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Encuestas/getEncuestas",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_hsurvey" },
            { "data": "name_hsurvey" },
            { "data": "obs_hsurvey" },
            { "data": "begindate_hsurvey" },
            { "data": "enddate_hsurvey" },
            { "data": "status_hsurvey" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Submit Form
    if (document.querySelector("#formEncuesta")) {
        const formEncuesta = document.querySelector("#formEncuesta");
        formEncuesta.onsubmit = async function (e) {
            e.preventDefault();
            const strName = document.querySelector('#txtName').value;
            const strBeginDate = document.querySelector('#txtBeginDate').value;
            const strEndDate = document.querySelector('#txtEndDate').value;

            if (strName == '' || strBeginDate == '' || strEndDate == '') {
                swal("Atención", "Todos los campos obligatorios deben ser completados.", "error");
                return;
            }

            const formData = new FormData(formEncuesta);
            // Uso de fetchData global
            const objData = await fetchData(BASE_URL_API + '/Encuestas/setEncuesta', 'POST', formData);

            if (objData?.status) {
                $('#modalFormEncuesta').modal("hide");
                formEncuesta.reset();
                swal("Encuestas", objData.msg, "success");
                tableEncuestas.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
    }
});

function openModal() {
    document.querySelector('#idSurvey').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nueva Encuesta";
    document.querySelector("#formEncuesta").reset();
    $('#modalFormEncuesta').modal('show');
}

async function fntEditEncuesta(idSurvey) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Encuesta";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Petición con fetchData global
    const objData = await fetchData(BASE_URL_API + '/Encuestas/getEncuesta/' + idSurvey);

    if (objData?.status) {
        document.querySelector("#idSurvey").value = objData.data.id_hsurvey;
        document.querySelector("#txtName").value = objData.data.name_hsurvey;
        document.querySelector("#txtObs").value = objData.data.obs_hsurvey;
        document.querySelector("#txtBeginDate").value = objData.data.begindate_hsurvey;
        document.querySelector("#txtEndDate").value = objData.data.enddate_hsurvey;

        $('#modalFormEncuesta').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
    }
}

function fntDelEncuesta(idSurvey) {
    swal({
        title: "Eliminar Encuesta",
        text: "¿Realmente quiere eliminar esta Encuesta?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, async function (isConfirm) {
        if (isConfirm) {
            let formData = new FormData();
            formData.append("idSurvey", idSurvey);

            const objData = await fetchData(BASE_URL_API + '/Encuestas/delEncuesta', 'POST', formData);

            if (objData?.status) {
                swal("Eliminado!", objData.msg, "success");
                tableEncuestas.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}

// ----------------------------------------------------------------------------------
// Lógica para Preguntas (bsurveys)
// ----------------------------------------------------------------------------------

let currentSurveyForQuestions = 0;

async function fntQuestions(idSurvey) {
    currentSurveyForQuestions = idSurvey;
    fntCancelEdit(); // Sets up form & ID
    $('#modalQuestions').modal('show');
    fntLoadQuestions();
}

function fntCancelEdit() {
    document.querySelector("#idQuestion").value = 0;
    document.querySelector("#formQuestion").reset();
    document.querySelector('#idSurveyQuestion').value = currentSurveyForQuestions; // Restaurar ID crítico
    document.querySelector("#containerOptions").innerHTML = "";
    document.querySelector("#divOptions").style.display = "none";

    // Restaurar botón Agregar
    const btnContainer = document.querySelector("#formQuestion div.text-center");
    if (btnContainer) {
        btnContainer.innerHTML = `<button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Agregar</button>`;
    }
}

async function fntLoadQuestions() {
    // Cargar lista de preguntas para la encuesta actual
    const container = document.querySelector("#containerQuestionsList");
    container.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';

    const objData = await fetchData(BASE_URL_API + '/Encuestas/getQuestions/' + currentSurveyForQuestions);

    if (objData?.status && Array.isArray(objData.data)) {
        let html = '';
        objData.data.forEach((q, index) => {
            let tipoBadge = "";
            let tipoLabel = "";
            let optionsHtml = "";

            if (q.type_bsurvey == 1) { tipoBadge = "badge-secondary"; tipoLabel = "Texto"; }
            else if (q.type_bsurvey == 2) { tipoBadge = "badge-info"; tipoLabel = "Fecha"; }
            else if (q.type_bsurvey == 3) { tipoBadge = "badge-warning"; tipoLabel = "Única"; }
            else if (q.type_bsurvey == 4) { tipoBadge = "badge-primary"; tipoLabel = "Múltiple"; }
            else if (q.type_bsurvey == 5) { tipoBadge = "badge-dark"; tipoLabel = "Compuesta"; }

            // Mostrar opciones si existen
            if (q.options_bsurvey && q.options_bsurvey !== "null") {
                try {
                    const opts = JSON.parse(q.options_bsurvey);
                    if (Array.isArray(opts) && opts.length > 0) {
                        optionsHtml = '<div class="mt-2 text-muted small"><ul class="mb-0 pl-3">';
                        opts.forEach(opt => {
                            let val = (typeof opt === 'object' && opt !== null) ? opt.nombre : opt;
                            optionsHtml += `<li>${val}</li>`;
                        });
                        optionsHtml += '</ul></div>';
                    }
                } catch (e) { }
            }

            // Botones de Orden (Visual)
            let btnUp = (index > 0) ? `<button class="btn btn-sm btn-outline-secondary" onclick="fntMoveQuestion(${q.id_bsurvey}, 'up')" title="Subir"><i class="fas fa-arrow-up"></i></button>` : '';
            let btnDown = (index < objData.data.length - 1) ? `<button class="btn btn-sm btn-outline-secondary" onclick="fntMoveQuestion(${q.id_bsurvey}, 'down')" title="Bajar"><i class="fas fa-arrow-down"></i></button>` : '';

            html += `
            <div class="card mb-2 border-light shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge ${tipoBadge} mr-2">${tipoLabel}</span>
                            <span class="font-weight-bold text-dark">${q.question_bsurvey}</span>
                            ${optionsHtml}
                        </div>
                        <div class="btn-group" role="group">
                            ${btnUp}
                            ${btnDown}
                            <button class="btn btn-sm btn-outline-primary" onclick="fntEditQuestion(${q.id_bsurvey})" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="fntDelQuestion(${q.id_bsurvey})" title="Eliminar"><i class="far fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
    } else {
        container.innerHTML = '<div class="alert alert-info text-center">No hay preguntas registradas.</div>';
    }
}

async function fntEditQuestion(idQuestion) {
    document.querySelector("#formQuestion").reset();

    // Cambiar botones
    const btnContainer = document.querySelector("#formQuestion div.text-center");
    if (btnContainer) {
        btnContainer.innerHTML = `<button class="btn btn-info" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Actualizar</button>
                                  <button class="btn btn-secondary" type="button" onclick="fntCancelEdit();"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</button>`;
    }

    const objData = await fetchData(BASE_URL_API + '/Encuestas/getQuestion/' + idQuestion);
    if (objData?.status) {
        const data = objData.data;
        document.querySelector("#idQuestion").value = data.id_bsurvey;
        document.querySelector("#txtQuestion").value = data.question_bsurvey;
        document.querySelector("#listType").value = data.type_bsurvey;

        // Renderizar opciones
        fntChangeType(); // Muestra/oculta div
        const container = document.querySelector("#containerOptions");
        container.innerHTML = ""; // Limpiar default

        if (data.options_bsurvey && data.options_bsurvey !== "null" && data.options_bsurvey !== "") {
            try {
                const opts = JSON.parse(data.options_bsurvey);
                if (Array.isArray(opts)) {
                    opts.forEach(opt => {
                        let val = "";
                        let hasInput = false;
                        if (typeof opt === 'object' && opt !== null) {
                            val = opt.nombre || "";
                            hasInput = opt.has_input || false;
                        } else {
                            val = opt; // Legacy string check
                        }
                        fntAddOptionValue(val, hasInput);
                    });
                }
            } catch (e) { }
        }

    } else {
        swal("Error", objData.msg, "error");
    }
}
function fntAddOptionValue(value, checked) {
    const container = document.querySelector("#containerOptions");
    const div = document.createElement("div");
    div.classList.add("input-group", "mb-2", "option-row");
    const isChecked = checked ? "checked" : "";
    div.innerHTML = `
        <input type="text" class="form-control option-input" value="${value}" placeholder="Opción / Etiqueta" required>
        <div class="input-group-append">
             <div class="input-group-text bg-white">
                <input type="checkbox" class="option-check" title="¿Requiere texto adicional?" ${isChecked}>
                <span class="ml-1 small d-none d-md-inline">Texto?</span>
            </div>
            <button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.remove();"><i class="fas fa-trash"></i></button>
        </div>
    `;
    container.appendChild(div);
}

async function fntMoveQuestion(idQuestion, direction) {
    // 1. Obtener estado actual para saber posiciones
    const objData = await fetchData(BASE_URL_API + '/Encuestas/getQuestions/' + currentSurveyForQuestions);

    if (objData?.status && Array.isArray(objData.data)) {
        const questions = objData.data;
        const index = questions.findIndex(q => q.id_bsurvey == idQuestion);

        if (index === -1) return;

        let targetIndex = -1;
        if (direction === 'up' && index > 0) {
            targetIndex = index - 1;
        } else if (direction === 'down' && index < questions.length - 1) {
            targetIndex = index + 1;
        }

        if (targetIndex !== -1) {
            const currentQ = questions[index];
            const targetQ = questions[targetIndex];

            // Intercambiar valores de orden
            // Nota: Usamos order_bsurvey de BD. Si es null o 0, asumimos index+1
            let orderCurrent = currentQ.order_bsurvey ? parseInt(currentQ.order_bsurvey) : index + 1;
            let orderTarget = targetQ.order_bsurvey ? parseInt(targetQ.order_bsurvey) : targetIndex + 1;

            // Simple swap logic: si los órdenes son iguales (o sucios), forzamos based on index visual
            // Para robustez: asignar al destino el orden del origen y viceversa

            // Forzamos update usando los indices visuales + 1 para garantizar secuencia limpia
            let newOrderCurrent = targetIndex + 1;
            let newOrderTarget = index + 1;

            // Update API calls
            let formData1 = new FormData();
            formData1.append("idQuestion", currentQ.id_bsurvey);
            formData1.append("order", newOrderCurrent);

            let formData2 = new FormData();
            formData2.append("idQuestion", targetQ.id_bsurvey);
            formData2.append("order", newOrderTarget);

            // Execute (serial or parallel)
            await fetchData(BASE_URL_API + '/Encuestas/setOrder', 'POST', formData1);
            await fetchData(BASE_URL_API + '/Encuestas/setOrder', 'POST', formData2);

            // Reload
            fntLoadQuestions();
        }
    }
}

function fntChangeType() {
    const type = document.querySelector("#listType").value;
    const divOptions = document.querySelector("#divOptions");
    const container = document.querySelector("#containerOptions");

    // Tipos que requieren opciones: 3 (Unica), 4 (Multiple), 5 (Compuesta)
    if (type == 3 || type == 4 || type == 5) {
        divOptions.style.display = "block";
        if (container.innerHTML.trim() == "") {
            fntAddOption(); // Agregar una por defecto si está vacío
        }
    } else {
        divOptions.style.display = "none";
        container.innerHTML = "";
    }
}

function fntAddOption() {
    const container = document.querySelector("#containerOptions");
    const div = document.createElement("div");
    div.classList.add("input-group", "mb-2", "option-row");
    div.innerHTML = `
        <input type="text" class="form-control option-input" placeholder="Opción / Etiqueta" required>
        <div class="input-group-append">
             <div class="input-group-text bg-white">
                <input type="checkbox" class="option-check" title="¿Requiere texto adicional? (Ej. Otro)">
                <span class="ml-1 small d-none d-md-inline">Texto?</span>
            </div>
            <button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.remove();"><i class="fas fa-trash"></i></button>
        </div>
    `;
    container.appendChild(div);
    div.querySelector(".option-input").focus();
}

// Submit Form Question
if (document.querySelector("#formQuestion")) {
    const formQuestion = document.querySelector("#formQuestion");
    formQuestion.onsubmit = async function (e) {
        e.preventDefault();

        const strQuestion = document.querySelector('#txtQuestion').value;
        const intType = document.querySelector('#listType').value;

        if (strQuestion == '') {
            swal("Atención", "Escribe la pregunta.", "error");
            return;
        }

        let arrOptions = [];
        // Validar opciones si aplica
        if (intType == 3 || intType == 4 || intType == 5) {
            const rows = document.querySelectorAll('.option-row');
            if (rows.length == 0) {
                swal("Atención", "Debes agregar al menos una opción.", "error");
                return;
            }

            rows.forEach((row, i) => {
                const val = row.querySelector('.option-input').value;
                const check = row.querySelector('.option-check').checked;
                arrOptions.push({
                    orden: i + 1,
                    nombre: val,
                    has_input: check
                });
            });
        }

        const formData = new FormData(formQuestion);
        // Sobreescribir txtOptions con el JSON estructurado si hay opciones
        if (arrOptions.length > 0) {
            formData.delete('txtOptions[]');
            formData.set('txtOptions', JSON.stringify(arrOptions));
        }

        const objData = await fetchData(BASE_URL_API + '/Encuestas/setQuestion', 'POST', formData);

        if (objData?.status) {
            formQuestion.reset();
            document.querySelector("#containerOptions").innerHTML = "";
            document.querySelector("#divOptions").style.display = "none";
            document.querySelector('#idSurveyQuestion').value = currentSurveyForQuestions; // Restaurar ID
            swal("Pregunta", "Agregada correctamente.", "success");
            fntLoadQuestions();
        } else {
            swal("Error", objData?.msg || "Error desconocido", "error");
        }
    };
}

function fntDelQuestion(idQuestion) {
    swal({
        title: "Eliminar Pregunta",
        text: "¿Eliminar esta pregunta?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "Cancelar",
        closeOnConfirm: true
    }, async function (isConfirm) {
        if (isConfirm) {
            let formData = new FormData();
            formData.append("idQuestion", idQuestion);
            const objData = await fetchData(BASE_URL_API + '/Encuestas/delQuestion', 'POST', formData);
            if (objData?.status) {
                fntLoadQuestions();
            } else {
                swal("Error", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}
