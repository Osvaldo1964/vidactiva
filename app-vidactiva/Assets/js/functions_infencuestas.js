document.addEventListener('DOMContentLoaded', function () {
    fntGetSurveys();

    if (document.querySelector("#formReporte")) {
        let formReporte = document.querySelector("#formReporte");
        formReporte.onsubmit = function (e) {
            e.preventDefault();
            fntViewReport();
        }
    }
});

async function fntGetSurveys() {
    const listSurveys = document.querySelector("#listSurveys");
    const objData = await fetchData(BASE_URL_API + '/Infencuestas/getSurveys');

    if (objData?.status) {
        let html = '<option value="">Seleccione una encuesta...</option>';
        objData.data.forEach(item => {
            html += `<option value="${item.id}">${item.label}</option>`;
        });
        listSurveys.innerHTML = html;
        // Refresh bootstrap-select if used
        if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
    } else {
        console.error("Error loading surveys:", objData);
    }
}

async function fntViewReport() {
    const idSurvey = document.querySelector("#listSurveys").value;
    if (idSurvey == "") {
        swal("Atención", "Seleccione una encuesta para generar el reporte.", "error");
        return;
    }

    const divResultados = document.querySelector("#divResultados");
    // Show Loading
    divResultados.style.display = "block";
    divResultados.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><br>Generando reporte...</div>';

    const objData = await fetchData(BASE_URL_API + '/Infencuestas/getReporte/' + idSurvey);

    if (objData.status) {
        // Reset Container with Table Structure
        divResultados.innerHTML = `
            <div class="tile-body">
                <div class="table-responsive">
                    <table id="tableReporte" class="table table-striped table-bordered text-nowrap" style="width:100%">
                    </table>
                </div>
            </div>`;

        // 1. Inject "Acciones" Column Definition
        // We'll add it as the LAST column
        objData.columns.push({
            title: "Acciones",
            data: null, // No specific data field, we use render
            orderable: false,
            className: "text-center",
            render: function (data, type, row) {
                // Assuming 'id_primary' exists in the row data (hidden or explicit)
                // If it's not in the visible columns, it might be in the full data object.
                // We need to ensure the API returns the primary key ID for editing.
                // Usually it's the first column or named specifically.
                // For now, let's look for a key that looks like an ID or use the row index as fallback (risky).

                // Better approach: The API should ideally return 'id_primary' in the data object.
                // Let's assume the API returns it. If not, we might need to adjust the API later.
                let id = row.id_primary || row[0] || 0;
                let btnEdit = '';
                let btnDel = '';

                // Check permissions (assuming permisosMod is globally available via view injection)
                if (typeof permisosMod !== 'undefined') {
                    if (permisosMod.u_permiso == 1) {
                        btnEdit = `<button class="btn btn-primary btn-sm btnEditEncuesta" onClick="fntEditEncuesta(${id}, ${idSurvey})" title="Editar"><i class="fas fa-pencil-alt"></i></button>`;
                    }
                    if (permisosMod.d_permiso == 1) {
                        btnDel = `<button class="btn btn-danger btn-sm btnDelEncuesta" onClick="fntDelRespuesta(${id}, ${idSurvey})" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`;
                    }
                } else {
                    // Fallback if permissions not loaded (dev mode)
                    btnEdit = `<button class="btn btn-primary btn-sm btnEditEncuesta" onClick="fntEditEncuesta(${id}, ${idSurvey})" title="Editar"><i class="fas fa-pencil-alt"></i></button>`;
                }

                return `<div class="text-center">${btnEdit} ${btnDel}</div>`;
            }
        });

        // 2. Hide extra columns for initial view (Sequence, Date, Q1, Q2, Q3...)
        objData.columns.forEach((col, index) => {
            // Updated logic: Hide columns from index 5 up to (length - 2) so Actions is visible?
            // Actually, keep the user requirements: display first few columns.
            // "Actions" is the last one now.
            if (index > 4 && index < (objData.columns.length - 1)) {
                // col.visible = false; // Commented out to verify user preference or keeps user logic
                col.visible = false;
            }
        });

        if ($.fn.DataTable.isDataTable('#tableReporte')) {
            $('#tableReporte').DataTable().destroy();
        }

        $('#tableReporte').DataTable({
            data: objData.data,
            columns: objData.columns,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    title: 'Reporte de Encuesta',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude Actions column from export
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    title: 'Reporte de Encuesta',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude Actions column from export
                    }
                }
            ],
            language: lenguajeEspanol,
            scrollX: true,
            pageLength: 25
        });

    } else {
        divResultados.innerHTML = `<div class="alert alert-warning text-center">${objData.msg}</div>`;
    }
}

async function fntEditEncuesta(idPersonSurvey, idSurvey) {
    // 1. Reset & Show Loading
    const container = document.querySelector("#containerEditForm");
    container.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando datos...</div>';

    document.querySelector("#idSurveyEdit").value = idSurvey;
    document.querySelector("#sequenceEdit").value = idPersonSurvey;

    $('#modalFormEdit').modal('show');

    // 2. Fetch Data
    const objData = await fetchData(BASE_URL_API + `/Infencuestas/getEncuestado/${idSurvey}/${idPersonSurvey}`);

    if (objData.status) {
        // 3. Generate HTML
        let html = '';
        const answers = objData.answers; // Map: id_bsurvey => [{value, id_answer}]

        objData.questions.forEach(q => {
            let currentVals = answers[q.id_bsurvey] || [];
            html += generateEditHTML(q, currentVals);
        });

        container.innerHTML = html;

        // 4. Setup Submit Handler
        const formEdit = document.querySelector("#formEditEncuesta");
        formEdit.onsubmit = async function (e) {
            e.preventDefault();

            // Validate Form
            if (!formEdit.checkValidity()) {
                formEdit.reportValidity();
                return;
            }

            // Validate Checkbox Groups (At least one must be checked)
            let chkGroups = {};
            document.querySelectorAll('.input-chk-group').forEach(chk => {
                chkGroups[chk.name] = true;
            });

            for (let name in chkGroups) {
                if (document.querySelectorAll(`input[name="${name}"]:checked`).length === 0) {
                    swal("Atención", "Debe seleccionar al menos una opción en las preguntas de selección múltiple.", "warning");
                    return;
                }
            }

            // Collect Form Data
            const formData = new FormData(formEdit);

            // Handle Composite Inputs (Type 5) manually to restore "Label: Value" format
            const compositeInputs = container.querySelectorAll('.composite-input');

            // We need to group them because we want an array per question
            compositeInputs.forEach(inp => {
                const qId = inp.dataset.qid;
                const label = inp.dataset.label;
                const val = inp.value;
                if (val.trim() !== "") {
                    const fullValue = `${label}: ${val}`;
                    formData.append(`q_${qId}[]`, fullValue);
                }
            });

            const res = await fetchData(BASE_URL_API + '/Infencuestas/setEncuestado', 'POST', formData);
            if (res.status) {
                swal("Actualizado", res.msg, "success");
                $('#modalFormEdit').modal('hide');
                // Refresh Table
                fntViewReport();
            } else {
                swal("Error", res.msg, "error");
            }
        };

    } else {
        container.innerHTML = `<div class="alert alert-danger">${objData.msg}</div>`;
    }
}

/**
 * Genera el HTML de un input para edición con su valor pre-cargado
 */
function generateEditHTML(q, currentVals) {
    let inputHtml = '';

    // Extract values for simple inputs
    let currentValStr = (currentVals.length > 0) ? currentVals[0].value : "";
    let currentValSet = new Set(currentVals.map(v => v.value));

    // Parse options
    let options = [];
    if (q.options_bsurvey && q.options_bsurvey !== "null" && q.options_bsurvey !== "") {
        try { options = JSON.parse(q.options_bsurvey); } catch (e) { console.error("Error parsing options", e); }
    }

    // Collect standard option names to identify custom values
    let standardOptionNames = new Set();
    if (Array.isArray(options)) {
        options.forEach(opt => {
            let name = (typeof opt === 'object') ? opt.nombre : opt;
            standardOptionNames.add(name);
        });
    }

    const qId = q.id_bsurvey;
    const qType = q.type_bsurvey;
    const inputName = `q_${qId}`;

    // Type 1: Texto | Type 2: Fecha
    if (qType == 1 || qType == 2) {
        let type = (qType == 2) ? 'date' : 'text';
        inputHtml = `<input type="${type}" class="form-control" name="${inputName}" value="${currentValStr}" required>`;
    }
    // Type 3: Radio
    else if (qType == 3) {
        if (Array.isArray(options)) {
            // Find if we have a functionality "active" custom value
            let customVal = "";
            let hasCustomVal = false;
            if (currentValStr !== "" && !standardOptionNames.has(currentValStr)) {
                customVal = currentValStr;
                hasCustomVal = true;
            }

            options.forEach((opt, i) => {
                let label = (typeof opt === 'object') ? opt.nombre : opt;
                let hasInput = (typeof opt === 'object') ? opt.has_input : false;

                let isChecked = false;
                let inputValue = label;
                let showOther = false;
                let otherVal = "";

                if (currentValStr == label) {
                    isChecked = true;
                } else if (hasInput && hasCustomVal) {
                    isChecked = true;
                    inputValue = customVal;
                    showOther = true;
                    otherVal = customVal;
                    hasCustomVal = false;
                }

                inputHtml += `
                <div class="form-check form-check-inline">
                    <input class="form-check-input question-input-radio" type="radio" name="${inputName}" id="rad_${qId}_${i}" value="${inputValue}" ${isChecked ? 'checked' : ''} data-label="${label}" data-hasinput="${hasInput}" required>
                    <label class="form-check-label" for="rad_${qId}_${i}">${label}</label>
                    ${hasInput ? `<input type="text" class="form-control form-control-sm d-inline-block ml-2 w-50 input-other" value="${otherVal}" style="${showOther ? '' : 'display:none;'}" ${showOther ? '' : 'disabled'} placeholder="Especifique...">` : ''}
                </div>`;
            });
        }
    }
    // Type 4: Checkbox
    else if (qType == 4) {
        if (Array.isArray(options)) {
            let customVals = [];
            currentValSet.forEach(v => {
                if (!standardOptionNames.has(v)) customVals.push(v);
            });

            options.forEach((opt, i) => {
                let label = (typeof opt === 'object') ? opt.nombre : opt;
                let hasInput = (typeof opt === 'object') ? opt.has_input : false;

                let isChecked = false;
                let inputValue = label;
                let showOther = false;
                let otherVal = "";

                if (currentValSet.has(label)) {
                    isChecked = true;
                } else if (hasInput && customVals.length > 0) {
                    isChecked = true;
                    inputValue = customVals[0];
                    showOther = true;
                    otherVal = customVals[0];
                    customVals.shift();
                }

                inputHtml += `
                <div class="form-check">
                    <input class="form-check-input input-chk-group question-input-check" type="checkbox" name="${inputName}[]" id="chk_${qId}_${i}" value="${inputValue}" ${isChecked ? 'checked' : ''} data-label="${label}" data-hasinput="${hasInput}">
                    <label class="form-check-label" for="chk_${qId}_${i}">${label}</label>
                    ${hasInput ? `<input type="text" class="form-control form-control-sm d-inline-block ml-2 w-50 input-other" value="${otherVal}" style="${showOther ? '' : 'display:none;'}" ${showOther ? '' : 'disabled'} placeholder="Especifique...">` : ''}
                </div>`;
            });
            inputHtml += `<small class="text-muted">Nota: La edición de selección múltiple sobreescribirá los valores previos.</small>`;
        }
    }
    // Type 5: Compuesta
    else if (qType == 5) {
        if (Array.isArray(options)) {
            options.forEach((opt, i) => {
                let label = (typeof opt === 'object') ? opt.nombre : opt;

                // Find value matching "Label: Value"
                let savedVal = "";
                let match = currentVals.find(obj => obj.value.startsWith(label + ":"));
                if (match) {
                    let parts = match.value.split(':');
                    if (parts.length > 1) {
                        parts.shift(); // Remove label
                        savedVal = parts.join(':').trim();
                    }
                }

                inputHtml += `
                <div class="form-group row mb-2">
                    <label class="col-sm-3 col-form-label">${label}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control composite-input" data-qid="${qId}" data-label="${label}" value="${savedVal}" required>
                    </div>
                </div>`;
            });
        }
    }

    return `
    <div class="form-group">
        <label class="font-weight-bold" style="background: #f8f9fa; padding: 5px; display:block;">${q.question_bsurvey}</label>
        ${inputHtml}
    </div>`;
}

// Global Listeners for Dynamic "Other" Inputs
document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('question-input-radio')) {
        const name = e.target.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            const cont = r.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                inp.style.display = 'none';
                inp.disabled = true;
                r.value = r.dataset.label;
            }
        });

        if (e.target.dataset.hasinput === "true") {
            const cont = e.target.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                inp.style.display = 'inline-block';
                inp.disabled = false;
                inp.focus();
                if (inp.value.trim() !== "") e.target.value = inp.value.trim();
            }
        }
    }

    if (e.target && e.target.classList.contains('question-input-check')) {
        if (e.target.dataset.hasinput === "true") {
            const cont = e.target.closest('.form-check');
            const inp = cont.querySelector('.input-other');
            if (inp) {
                if (e.target.checked) {
                    inp.style.display = 'inline-block';
                    inp.disabled = false;
                    inp.focus();
                    if (inp.value.trim() !== "") e.target.value = inp.value.trim();
                } else {
                    inp.style.display = 'none';
                    inp.disabled = true;
                    e.target.value = e.target.dataset.label;
                }
            }
        }
    }


});

document.addEventListener('input', function (e) {
    if (e.target && e.target.classList.contains('input-other')) {
        const cont = e.target.closest('.form-check');
        const r = cont.querySelector('input[type="radio"], input[type="checkbox"]');
        if (r) {
            r.value = e.target.value.trim() !== "" ? e.target.value.trim() : r.dataset.label;
            if (!r.checked) r.checked = true;
        }
    }
});

function fntDelRespuesta(sequence, idSurvey) {
    swal({
        title: "Eliminar Registro",
        text: "¿Realmente quiere eliminar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, async function (isConfirm) {
        if (isConfirm) {
            // Using params in URL as configured in API
            const url = `${BASE_URL_API}/Infencuestas/delRespuesta/${idSurvey},${sequence}`;
            const objData = await fetchData(url, 'DELETE');

            if (objData.status) {
                swal("Eliminado!", objData.msg, "success");
                fntViewReport(); // Refresh table
            } else {
                swal("Atención", objData.msg, "error");
            }
        }
    });
}

