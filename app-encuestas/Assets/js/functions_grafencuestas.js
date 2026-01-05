
let myChart = null; // Store chart instance
let currentChartData = null; // Store data for export

document.addEventListener('DOMContentLoaded', function () {
    fntGetSurveys();

    // Listener for Survey Change
    const listSurveys = document.querySelector("#listSurveys");
    listSurveys.addEventListener('change', function () {
        let idSurvey = this.value;
        fntGetQuestions(idSurvey);
        // Clear chart when changing survey as questions change
        if (myChart) {
            myChart.destroy();
            myChart = null;
        }
        document.querySelector("#divGrafica").style.display = "none";
        currentChartData = null;
    });

    // Listener for Interactive Chart Update
    const listQuestions = document.querySelector("#listQuestions");
    listQuestions.addEventListener('change', function () {
        if (this.value !== "") {
            fntGenerateChart();
        }
    });

    const listChartType = document.querySelector("#listChartType");
    listChartType.addEventListener('change', function () {
        // Only regenerate if a question is already selected
        if (listQuestions.value !== "") {
            fntGenerateChart();
        }
    });

    // Form Submit (Manual Trigger)
    const formGrafica = document.querySelector("#formGrafica");
    formGrafica.onsubmit = function (e) {
        e.preventDefault();
        fntGenerateChart();
    }
});

async function fntGetSurveys() {
    const listSurveys = document.querySelector("#listSurveys");
    const objData = await fetchData(BASE_URL_API + '/Grafencuestas/getSurveys');

    if (objData?.status) {
        let html = '<option value="">Seleccione una encuesta...</option>';
        objData.data.forEach(item => {
            html += `<option value="${item.id}">${item.label}</option>`;
        });
        listSurveys.innerHTML = html;
        if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
    } else {
        console.error("Error loading surveys");
    }
}

async function fntGetQuestions(idSurvey) {
    const listQuestions = document.querySelector("#listQuestions");

    if (idSurvey == "") {
        listQuestions.innerHTML = '<option value="">Seleccione encuesta primero...</option>';
        listQuestions.disabled = true;
        if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
        return;
    }

    const objData = await fetchData(BASE_URL_API + '/Grafencuestas/getQuestions/' + idSurvey);

    if (objData?.status) {
        let html = '<option value="">Seleccione una pregunta...</option>';
        objData.data.forEach(item => {
            html += `<option value="${item.id_bsurvey}">${item.question_bsurvey}</option>`;
        });
        listQuestions.innerHTML = html;
        listQuestions.disabled = false;
        if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
    } else {
        listQuestions.innerHTML = '<option value="">No hay preguntas disponibles</option>';
        listQuestions.disabled = true;
        if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
    }
}

async function fntGenerateChart() {
    const idSurvey = document.querySelector("#listSurveys").value;
    const idQuestion = document.querySelector("#listQuestions").value;
    const chartType = document.querySelector("#listChartType").value;
    const divGrafica = document.querySelector("#divGrafica");
    const chartTitle = document.querySelector("#chartTitle");

    if (idSurvey == "" || idQuestion == "") {
        swal("Atención", "Debe seleccionar una encuesta y una pregunta.", "warning");
        return;
    }

    // Show loading
    divGrafica.style.display = "block";
    divGrafica.scrollIntoView({ behavior: 'smooth' });

    const objData = await fetchData(BASE_URL_API + `/Grafencuestas/getData/${idSurvey}/${idQuestion}`);

    if (objData.status) {
        // Store data for export
        currentChartData = objData.data;

        // Prepare Data
        const labels = objData.chartData.labels;
        const counts = objData.chartData.counts;

        // Colors Palette (Viridian inspired + distinct colors)
        const baseColors = [
            '#40826D', '#2C3E50', '#E74C3C', '#F1C40F', '#8E44AD',
            '#3498DB', '#E67E22', '#16A085', '#95A5A6', '#D35400',
            '#2ECC71', '#C0392B', '#9B59B6', '#2980B9', '#7F8C8D'
        ];

        let bgColors = [];
        for (let i = 0; i < labels.length; i++) {
            bgColors.push(baseColors[i % baseColors.length]);
        }

        // Destroy previous chart if exists
        if (myChart) {
            myChart.destroy();
        }

        const ctx = document.getElementById('myChart').getContext('2d');

        // Configuration for Chart.js
        let config = {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Respuestas',
                    data: counts,
                    backgroundColor: bgColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    display: (chartType !== 'bar' && chartType !== 'horizontalBar') // Hide legend for simple bars to save space
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0 // No decimals for counts
                        },
                        display: (chartType === 'bar' || chartType === 'line' || chartType === 'horizontalBar')
                    }],
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        display: (chartType === 'bar' || chartType === 'line' || chartType === 'horizontalBar')
                    }]
                },
                title: {
                    display: true,
                    text: 'Distribución de Respuestas',
                    fontSize: 16
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            let label = data.labels[tooltipItem.index] || '';
                            let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            let total = 0;
                            data.datasets[tooltipItem.datasetIndex].data.forEach(element => {
                                total += element;
                            });
                            let percentage = Math.round((value / total) * 100) + '%';

                            if (label) {
                                label += ': ';
                            }
                            return label + value + ' (' + percentage + ')';
                        }
                    }
                }
            }
        };

        // Specific adjustments for Pie/Donut
        if (chartType === 'pie' || chartType === 'doughnut' || chartType === 'polarArea') {
            config.options.scales = {}; // Remove axes
        }

        myChart = new Chart(ctx, config);

        // Update Title with Question Text
        let selQ = document.querySelector("#listQuestions");
        let qText = selQ.options[selQ.selectedIndex].text;
        chartTitle.innerHTML = `<i class="fa fa-question-circle"></i> ${qText}`;

    } else {
        swal("Información", objData.msg, "info");
        currentChartData = null;
        // Clear canvas if no data
        if (myChart) {
            myChart.destroy();
            myChart = null;
        }
    }
}

function fntPrintChart() {
    window.print();
}

function fntExportChartData() {
    if (!currentChartData || currentChartData.length === 0) {
        swal("Atención", "No hay datos para exportar.", "warning");
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,";
    // Header
    csvContent += "Respuesta (Etiqueta),Cantidad\r\n";

    // Data Rows
    currentChartData.forEach(function (row) {
        // Escape quotes to be safe
        let label = row.label ? row.label.replace(/"/g, '""') : "Sin respuesta";
        csvContent += `"${label}",${row.cantidad}\r\n`;
    });

    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    // Get question name for filename
    let selQ = document.querySelector("#listQuestions");
    let qText = selQ.options[selQ.selectedIndex].text.replace(/[^a-zA-Z0-9]/g, "_").substring(0, 30);

    link.setAttribute("download", `Reporte_Grafic_${qText}.csv`);
    document.body.appendChild(link); // Required for FF
    link.click();

    document.body.removeChild(link);
}
