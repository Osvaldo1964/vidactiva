<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-pie-chart"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/grafencuestas"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formGrafica" name="formGrafica">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-4">
                                <label for="listSurveys">Seleccionar Encuesta</label>
                                <select class="form-control selectpicker" id="listSurveys" name="listSurveys" data-live-search="true" required>
                                    <!-- Options loaded via JS -->
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="listQuestions">Seleccionar Pregunta</label>
                                <select class="form-control selectpicker" id="listQuestions" name="listQuestions" data-live-search="true" required disabled>
                                    <option value="">Seleccione encuesta primero...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="listChartType">Tipo de Gráfica</label>
                                <select class="form-control selectpicker" id="listChartType" name="listChartType" required>
                                    <option value="bar">Barras (Vertical)</option>
                                    <option value="horizontalBar">Barras (Horizontal)</option>
                                    <option value="pie">Pastel (Pie)</option>
                                    <option value="doughnut">Donas</option>
                                    <option value="polarArea">Area Polar</option>
                                    <option value="line">Línea</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-fw fa-lg fa-bar-chart"></i> Generar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tile" id="divGrafica" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="tile-title mb-0" id="chartTitle">Resultados</h3>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-secondary" onclick="fntPrintChart()"><i class="fas fa-print"></i> Imprimir</button>
                        <button type="button" class="btn btn-success" onclick="fntExportChartData()"><i class="fas fa-file-excel"></i> Exportar Datos</button>
                    </div>
                </div>
                <div class="tile-body">
                    <div style="position: relative; height:60vh; width:100%">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>