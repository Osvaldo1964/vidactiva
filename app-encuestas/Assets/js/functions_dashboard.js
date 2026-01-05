
document.addEventListener('DOMContentLoaded', function () {
    fntGetDashboard();
});

async function fntGetDashboard() {
    try {
        const response = await fetch(BASE_URL_API + '/dashboard/getResumen', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();

        if (data.status) {
            let res = data.data;

            // Widgets
            if (document.getElementById('lblTotalElectores')) document.getElementById('lblTotalElectores').innerText = res.total_electores;
            if (document.getElementById('lblTotalLideres')) document.getElementById('lblTotalLideres').innerText = res.total_lideres;
            if (document.getElementById('lblTotalVotos')) document.getElementById('lblTotalVotos').innerText = res.total_votos;
            if (document.getElementById('lblMetaGlobal')) document.getElementById('lblMetaGlobal').innerText = res.meta_global;

            if (document.getElementById('lblPorcentajeMeta')) {
                document.getElementById('lblPorcentajeMeta').innerText = res.porcentaje_meta + "%";
                // Colorize meta widget based on progress
                /*
                let widgetMeta = document.getElementById('lblPorcentajeMeta').closest('.widget-small');
                if(res.porcentaje_meta >= 100) widgetMeta.classList.add('success');
                */
            }

            // --- Gráfico Top Líderes ---
            let lideresNombres = [];
            let lideresCant = [];
            if (res.top_lideres) {
                lideresNombres = res.top_lideres.map(l => l.nombre);
                lideresCant = res.top_lideres.map(l => l.cantidad);
            }

            if (document.getElementById('chartLideres')) {
                var ctxLideres = document.getElementById('chartLideres').getContext('2d');
                new Chart(ctxLideres, {
                    type: 'horizontalBar', // Requiere Chart.js v2.x
                    data: {
                        labels: lideresNombres,
                        datasets: [{
                            label: 'Electores Vinculados',
                            data: lideresCant,
                            backgroundColor: '#40826D', // Viridian
                            borderColor: '#2E5C4D',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            xAxes: [{ ticks: { beginAtZero: true } }]
                        }
                    }
                });
            }

            // --- Gráfico Distribución Municipios ---
            let muniNombres = [];
            let muniCant = [];
            if (res.dist_municipios) {
                muniNombres = res.dist_municipios.map(m => m.municipio);
                muniCant = res.dist_municipios.map(m => m.cantidad);
            }

            if (document.getElementById('chartMunicipios')) {
                var ctxMuni = document.getElementById('chartMunicipios').getContext('2d');
                new Chart(ctxMuni, {
                    type: 'doughnut',
                    data: {
                        labels: muniNombres,
                        datasets: [{
                            data: muniCant,
                            // Paleta Viridian
                            backgroundColor: ['#40826D', '#2E5C4D', '#6B9E8E', '#1C3B30', '#8FC9B5', '#5F9EA0']
                        }]
                    },
                    options: {
                        legend: { position: 'right' }
                    }
                });
            }
        }

    } catch (e) { console.error(e); }
}
