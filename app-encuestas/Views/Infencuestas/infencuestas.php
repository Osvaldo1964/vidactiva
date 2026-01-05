<?php headerAdmin($data); ?>
<script>
    const permisosMod = <?= json_encode($_SESSION['permisosMod']); ?>;
</script>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text-o"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/infencuestas"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formReporte" name="formReporte" class="form-row align-items-end">
                        <div class="form-group col-md-4">
                            <label for="listSurveys">Seleccionar Encuesta</label>
                            <select class="form-control" id="listSurveys" name="listSurveys" required>
                                <!-- Options loaded via JS -->
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-fw fa-lg fa-check-circle"></i> Generar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tile" id="divResultados" style="display: none;">
                <div class="tile-body">
                    <div class="table-responsive">
                        <!-- Table structure will be rebuilt by JS -->
                        <table id="tableReporte" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <!-- Dynamic Headers -->
                            </thead>
                            <tbody>
                                <!-- Dynamic Rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- Modal Edición -->
<div class="modal fade" id="modalFormEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="titleModal">Editar Respuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditEncuesta" name="formEditEncuesta">
                    <input type="hidden" id="idSurveyEdit" name="idSurvey">
                    <input type="hidden" id="sequenceEdit" name="sequence">

                    <div id="containerEditForm">
                        <!-- Dynamic Content -->
                    </div>

                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-info" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Actualizar</span></button>
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php footerAdmin($data); ?>