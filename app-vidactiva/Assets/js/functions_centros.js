// functions_centros.js

let tableCentros;
let allMunis = [];

document.addEventListener('DOMContentLoaded', async function () {

    // 1. Cargar Dptos y Munis (Exclusivo de Centros)
    await loadDptosMunis();

    // 2. Inicialización de DataTables
    tableCentros = $('#tableCentros').DataTable({
        "processing": true,
        "language": lenguajeEspanol,
        "ajax": {
            "url": BASE_URL_API + "/Centros/getCentros",
            "type": "GET",
            // "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }, // Uncomment if API requires token
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_centro" },
            { "data": "nombre_centro" },
            { "data": "telefono_centro" },
            { "data": "email_centro" },
            { "data": "dpto_centro", "render": function (data) { return getDptoName(data); } },
            { "data": "muni_centro", "render": function (data) { return getMuniName(data); } },
            { "data": "direccion_centro" },
            { "data": "poblacion_centro" },
            { "data": "estado_centro" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 3. Evento Change Departamento
    document.querySelector("#listDpto").addEventListener('change', function (e) {
        let dptoId = this.value;
        filterMunis(dptoId);
    });

    // 4. Submit Form
    if (document.querySelector("#formCentro")) {
        const formCentro = document.querySelector("#formCentro");
        formCentro.onsubmit = async function (e) {
            e.preventDefault();

            // Validaciones básicas
            let strNombre = document.querySelector('#txtNombre').value;
            let strTelefono = document.querySelector('#txtTelefono').value;
            let strEmail = document.querySelector('#txtEmail').value;
            let intPoblacion = document.querySelector('#txtPoblacion').value;
            let listDpto = document.querySelector('#listDpto').value;
            let listMuni = document.querySelector('#listMuni').value;

            if (strNombre == '' || strTelefono == '' || strEmail == '' || intPoblacion == '' || listDpto == '' || listMuni == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return;
            }

            const formData = new FormData(formCentro);
            const objData = await fetchData(BASE_URL_API + '/Centros/setCentro', 'POST', formData);

            if (objData?.status) {
                $('#modalFormCentro').modal("hide");
                formCentro.reset();
                swal("Centros", objData.msg, "success");
                tableCentros.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
    }
});

function openModal() {
    document.querySelector('#idCentro').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Centro";
    document.querySelector("#formCentro").reset();
    $('#modalFormCentro').modal('show');

    // Reset selects
    $('#listDpto').val('').selectpicker('refresh');
    $('#listMuni').empty().selectpicker('refresh');
    $('#listStatus').val('1').selectpicker('refresh');
}

async function fntEditCentro(idcentro) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Centro";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const objData = await fetchData(BASE_URL_API + '/Centros/getCentro/' + idcentro);

    if (objData?.status) {
        let data = objData.data;
        document.querySelector("#idCentro").value = data.id_centro;
        document.querySelector("#txtNombre").value = data.nombre_centro;
        document.querySelector("#txtTelefono").value = data.telefono_centro;
        document.querySelector("#txtEmail").value = data.email_centro;
        document.querySelector("#txtPoblacion").value = data.poblacion_centro;
        document.querySelector("#txtDireccion").value = data.direccion_centro;

        // Handle Selects logic
        $('#listDpto').val(data.dpto_centro).selectpicker('refresh');
        filterMunis(data.dpto_centro, data.muni_centro);

        $('#listStatus').val(data.estado_centro).selectpicker('refresh');

        $('#modalFormCentro').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
    }
}

function fntDelCentro(idcentro) {
    swal({
        title: "Eliminar Centro",
        text: "¿Realmente quiere eliminar el Centro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, async function (isConfirm) {
        if (isConfirm) {
            let formData = new FormData();
            formData.append("idCentro", idcentro);
            const objData = await fetchData(BASE_URL_API + '/Centros/delCentro', 'POST', formData);

            if (objData?.status) {
                swal("Eliminado!", objData.msg, "success");
                tableCentros.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}

// --- HELPERS ---

let dptosData = [];

async function loadDptosMunis() {
    try {
        // Use PHP proxy method to avoid CORS on static file
        let jsonPath = BASE_URL_API + '/Centros/getConfig';

        // Fallback or explicit path might be needed depending on server config
        // Assuming BASE_URL/api-encuestas is reachable
        const res = await fetch(jsonPath);
        if (!res.ok) throw new Error("Could not load Config.json");
        const data = await res.json();

        dptosData = data.dptos;
        allMunis = data.munis;

        let dptosHtml = '<option value="">Seleccione Departamento</option>';
        data.dptos.forEach(dpto => {
            dptosHtml += `<option value="${dpto.iddpto}">${dpto.namedpto}</option>`;
        });
        document.querySelector("#listDpto").innerHTML = dptosHtml;
        $('#listDpto').selectpicker('refresh');

    } catch (error) {
        console.error("Error loading locations:", error);
    }
}

function filterMunis(dptoId, selectedMuniId = null) {
    let munisHtml = '<option value="">Seleccione Municipio</option>';
    const filteredMunis = allMunis.filter(muni => muni.dptomuni == dptoId);

    filteredMunis.forEach(muni => {
        let selected = (selectedMuniId && selectedMuniId == muni.idmuni) ? 'selected' : '';
        munisHtml += `<option value="${muni.idmuni}" ${selected}>${muni.namemuni}</option>`;
    });
    document.querySelector("#listMuni").innerHTML = munisHtml;
    $('#listMuni').selectpicker('refresh');
}

function getDptoName(id) {
    let d = dptosData.find(x => x.iddpto == id);
    return d ? d.namedpto : id;
}

function getMuniName(id) {
    let m = allMunis.find(x => x.idmuni == id);
    return m ? m.namemuni : id;
}
