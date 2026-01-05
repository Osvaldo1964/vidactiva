document.addEventListener('DOMContentLoaded', function () {
    fntGetSurveys();

    // Validate Form
    if (document.querySelector("#formRegistro")) {
        let formRegistro = document.querySelector("#formRegistro");
        formRegistro.onsubmit = function (e) {
            e.preventDefault();
            fntSaveAnswers();
        }
    }
});

async function fntGetSurveys() {
    const listSurveys = document.querySelector("#listSurveys");
    const objData = await fetchData(BASE_URL_API + '/Registro/getSurveys');
    if (objData.status) {
        let html = '<option value="">Seleccione una encuesta...</option>';
        objData.data.forEach(item => {
            html += `<option value="${item.id_hsurvey}">${item.name_hsurvey}</option>`;
        });
        listSurveys.innerHTML = html;
    }
}

async function fntLoadForm() {
    const idSurvey = document.querySelector("#listSurveys").value;
    const containerInfo = document.querySelector("#containerFormInfo"); // New container
    const container = document.querySelector("#containerForm");
    const divActions = document.querySelector("#divActions");

    if (idSurvey == "") {
        containerInfo.style.display = "none"; // Hide entire card
        container.style.display = "none";
        divActions.style.display = "none";
        container.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin"></i> Cargando formulario...</div>';
        return;
    }

    containerInfo.style.display = "block"; // Show card
    container.style.display = "block";
    divActions.style.display = "none"; // Hide buttons until loaded

    // Reuse Encuestas API to get questions
    const objData = await fetchData(BASE_URL_API + '/Encuestas/getQuestions/' + idSurvey);

    if (objData.status && Array.isArray(objData.data)) {
        let html = '';
        objData.data.forEach(q => {
            html += generateQuestionHTML(q);
        });
        container.innerHTML = html;
        divActions.style.display = "block";
    } else {
        container.innerHTML = '<div class="alert alert-info text-center">No hay preguntas configuradas para esta encuesta.</div>';
    }
}

function generateQuestionHTML(q) {
    let inputHtml = '';
    let required = 'required'; // Assume all required for now, or check logical business rule? Let's make them required for basic version unless empty options logic exists.

    // Parse options
    let options = [];
    if (q.options_bsurvey && q.options_bsurvey !== "null") {
        try { options = JSON.parse(q.options_bsurvey); } catch (e) { }
    }

    const qId = q.id_bsurvey;
    const qType = q.type_bsurvey;

    // Type 1: Texto Abierto
    if (qType == 1) {
        inputHtml = `<input type="text" class="form-control question-input form-control-sm survey-input-sm" data-id="${qId}" data-type="${qType}">`;
    }
    // Type 2: Fecha
    else if (qType == 2) {
        inputHtml = `<input type="date" class="form-control question-input form-control-sm survey-input-sm" data-id="${qId}" data-type="${qType}">`;
    }
    // Type 3: Seleccion Unica (Radio)
    else if (qType == 3) {
        if (Array.isArray(options)) {
            options.forEach((opt, i) => {
                let val = (typeof opt === 'object') ? opt.nombre : opt;
                let hasInput = (typeof opt === 'object') ? opt.has_input : false;

                inputHtml += `
                <div class="form-check mb-1">
                    <input class="form-check-input question-input-radio" type="radio" name="radio_${qId}" id="radio_${qId}_${i}" value="${val}" data-id="${qId}" data-type="${qType}" data-hasinput="${hasInput}">
                    <label class="form-check-label survey-option-label" for="radio_${qId}_${i}">${val}</label>
                    ${hasInput ? `<input type="text" class="form-control form-control-sm d-inline-block ml-2 w-50 input-other survey-input-sm" style="display:none;" disabled placeholder="Especifique...">` : ''}
                </div>`;
            });
        }
    }
    // Type 4: Seleccion Multiple (Checkbox)
    else if (qType == 4) {
        if (Array.isArray(options)) {
            options.forEach((opt, i) => {
                let val = (typeof opt === 'object') ? opt.nombre : opt;
                let hasInput = (typeof opt === 'object') ? opt.has_input : false;

                inputHtml += `
                <div class="form-check mb-1">
                    <input class="form-check-input question-input-check" type="checkbox" name="check_${qId}[]" id="check_${qId}_${i}" value="${val}" data-id="${qId}" data-type="${qType}" data-hasinput="${hasInput}">
                    <label class="form-check-label survey-option-label" for="check_${qId}_${i}">${val}</label>
                    ${hasInput ? `<input type="text" class="form-control form-control-sm d-inline-block ml-2 w-50 input-other survey-input-sm" style="display:none;" disabled placeholder="Especifique...">` : ''}
                </div>`;
            });
        }
    }
    // Type 5: Compuesta (Inputs based on options)
    else if (qType == 5) {
        if (Array.isArray(options)) {
            options.forEach((opt, i) => {
                let val = (typeof opt === 'object') ? opt.nombre : opt;
                inputHtml += `
                <div class="form-group row mb-2">
                    <label class="col-sm-4 col-md-3 col-form-label font-weight-bold survey-option-label" style="align-self: center;">${val}</label>
                    <div class="col-sm-8 col-md-9">
                        <input type="text" class="form-control form-control-sm question-input-compuesta survey-input-sm" data-label="${val}" data-id="${qId}" data-type="${qType}">
                    </div>
                </div>`;
            });
        }
    }

    return `
    <div class="card mb-3 shadow-sm question-card" data-id="${qId}" data-type="${qType}">
        <div class="card-header bg-light py-2 survey-card-header">
            <h6 class="mb-0 text-dark font-weight-bold">${q.question_bsurvey}</h6>
        </div>
        <div class="card-body py-3">
            ${inputHtml}
        </div>
    </div>`;
}

async function fntSaveAnswers() {
    const idSurvey = document.querySelector("#listSurveys").value;
    if (idSurvey == "") return;

    let answers = [];
    const questionCards = document.querySelectorAll(".question-card");
    let isValid = true;

    questionCards.forEach(card => {
        const qId = card.getAttribute('data-id');
        const qType = card.getAttribute('data-type');
        let val = null;

        if (qType == 1 || qType == 2) {
            val = card.querySelector(".question-input").value;
        }
        else if (qType == 3) {
            // Radio
            const checked = card.querySelector("input[type='radio']:checked");
            if (checked) {
                val = checked.value;
                // If has extra input enabled
                if (checked.dataset.hasinput === "true") {
                    const container = checked.closest('.form-check');
                    const otherInput = container.querySelector('.input-other');
                    if (otherInput) {
                        const otherVal = otherInput.value.trim();
                        if (otherVal !== "") val = otherVal;
                        else val = checked.value;
                    }
                }
            }
        }
        else if (qType == 4) {
            // Checkbox - array
            val = [];
            const checks = card.querySelectorAll("input[type='checkbox']:checked");
            checks.forEach(chk => {
                let v = chk.value;
                if (chk.dataset.hasinput === "true") {
                    const container = chk.closest('.form-check');
                    const otherInput = container.querySelector('.input-other');
                    if (otherInput) {
                        const otherVal = otherInput.value.trim();
                        if (otherVal !== "") v = otherVal;
                    }
                }
                val.push(v);
            });
            if (val.length == 0) val = null;
        }
        else if (qType == 5) {
            // Compuesta - Multiple texts
            val = [];
            const inputs = card.querySelectorAll(".question-input-compuesta");
            inputs.forEach(inp => {
                if (inp.value.trim() !== "") {
                    val.push(inp.getAttribute('data-label') + ": " + inp.value.trim());
                }
            });
            if (val.length == 0) val = null;
        }

        // Basic Validation (Simple check empty)
        if (val === null || val === "" || (Array.isArray(val) && val.length === 0)) {
            isValid = false;
            card.classList.add("border-danger");
        } else {
            // Special check for Type 5: All fields must be filled
            if (qType == 5) {
                const totalInputs = card.querySelectorAll(".question-input-compuesta").length;
                if (val.length < totalInputs) {
                    isValid = false;
                    card.classList.add("border-danger");
                } else {
                    card.classList.remove("border-danger");
                    answers.push({
                        idQuestion: qId,
                        type: qType,
                        value: val
                    });
                }
            } else {
                card.classList.remove("border-danger");
                answers.push({
                    idQuestion: qId,
                    type: qType,
                    value: val
                });
            }
        }
    });

    if (!isValid) {
        swal("Atención", "Por favor responde todas las preguntas marcadas en rojo.", "error");
        return;
    }

    if (answers.length === 0) {
        swal("Atención", "No hay respuestas para guardar.", "warning");
        return;
    }

    const formData = new FormData();
    formData.append("idSurvey", idSurvey);
    formData.append("answers", JSON.stringify(answers));

    const objData = await fetchData(BASE_URL_API + '/Registro/saveRespuestas', 'POST', formData);

    if (objData.status) {
        swal({
            title: "Guardado",
            text: `${objData.msg}\n\nID Usuario: ${localStorage.getItem('idUser')}\nSecuencia: ${objData.sequence}`,
            type: "success",
            confirmButtonText: "Continuar",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                setTimeout(fntClearForm, 200);
            }
        });
    } else {
        swal("Error", objData.msg, "error");
    }
}

// Function to clear answers but keep the form ready for next entry
function fntClearForm() {
    // 1. Clear all inputs
    const inputs = document.querySelectorAll(".question-input, .question-input-compuesta, .input-other");
    inputs.forEach(inp => {
        inp.value = "";
        inp.classList.remove("border-danger");
    });

    // 2. Uncheck radios and checkboxes
    const checks = document.querySelectorAll(".question-input-radio, .question-input-check");
    checks.forEach(chk => {
        chk.checked = false;
    });

    // 3. Reset 'Other' inputs visibility and state
    const otherInputs = document.querySelectorAll(".input-other");
    otherInputs.forEach(inp => {
        inp.style.display = "none";
        inp.disabled = true;
    });

    // 4. Remove danger borders from cards
    const cards = document.querySelectorAll(".question-card");
    cards.forEach(card => card.classList.remove("border-danger"));

    // 5. Focus on first input
    const firstInput = document.querySelector(".question-input, .question-input-radio, .question-input-check");
    if (firstInput) {
        firstInput.focus();
        // Scroll to top of form container smoothly
        if (document.getElementById("containerFormInfo")) {
            document.getElementById("containerFormInfo").scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }
}

function fntResetForm() {
    document.querySelector("#listSurveys").value = "";
    document.querySelector("#containerForm").innerHTML = "";
    document.querySelector("#containerForm").style.display = "none";
    document.querySelector("#divActions").style.display = "none";

    const containerInfo = document.querySelector("#containerFormInfo");
    if (containerInfo) containerInfo.style.display = "none";
}

// Logic for "Other" input visibility (Event delegation)
document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('question-input-radio')) {
        // Radios: Hide all 'others' in this question group, show for this one if applicable
        const name = e.target.name;
        // Select all radios with same name
        const radios = document.querySelectorAll(`input[name="${name}"]`);
        radios.forEach(r => {
            const cont = r.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                inp.style.display = 'none';
                inp.disabled = true; // Disable
                inp.value = '';
            }
        });

        // If current checked has input
        if (e.target.dataset.hasinput === "true") {
            const cont = e.target.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                inp.style.display = 'inline-block';
                inp.disabled = false; // Enable
                inp.focus();
            }
        }
    }

    // Checkboxes: Toggle own input
    if (e.target && e.target.classList.contains('question-input-check')) {
        if (e.target.dataset.hasinput === "true") {
            const cont = e.target.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                if (e.target.checked) {
                    inp.style.display = 'inline-block';
                    inp.disabled = false; // Enable
                    inp.focus();
                } else {
                    inp.style.display = 'none';
                    inp.disabled = true; // Disable
                    inp.value = '';
                }
            }
        }
    }
});
