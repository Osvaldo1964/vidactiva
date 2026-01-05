<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-poll-h"></i> <?= $data['page_title'] ?>
                <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nuevo</button>
            </h1>
            <p>Administración de Encuestas</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/encuestas"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableEncuestas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Observaciones</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modalFormEncuesta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nueva Encuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEncuesta" name="formEncuesta" class="form-horizontal">
                    <input type="hidden" id="idSurvey" name="idSurvey" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="txtName">Nombre de la Encuesta</label>
                            <input type="text" class="form-control" id="txtName" name="txtName" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtBeginDate">Fecha Inicio</label>
                            <input type="date" class="form-control" id="txtBeginDate" name="txtBeginDate" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtEndDate">Fecha Fin</label>
                            <input type="date" class="form-control" id="txtEndDate" name="txtEndDate" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtObs">Observaciones</label>
                        <textarea class="form-control" id="txtObs" name="txtObs" rows="3"></textarea>
                    </div>
                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preguntas -->
<div class="modal fade" id="modalQuestions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModalQuestions">Gestionar Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Formulario de Preguntas -->
                    <div class="col-md-4">
                        <form id="formQuestion" name="formQuestion">
                            <input type="hidden" id="idSurveyQuestion" name="idSurveyQuestion" value="">
                            <input type="hidden" id="idQuestion" name="idQuestion" value="">
                            <div class="form-group">
                                <label for="txtQuestion">Pregunta</label>
                                <textarea class="form-control" id="txtQuestion" name="txtQuestion" rows="2" required=""></textarea>
                            </div>
                            <div class="form-group">
                                <label for="listType">Tipo de Respuesta</label>
                                <select class="form-control" id="listType" name="listType" onchange="fntChangeType();" required="">
                                    <option value="1">Texto Abierto</option>
                                    <option value="2">Fecha</option>
                                    <option value="3">Selección Única</option>
                                    <option value="4">Selección Múltiple</option>
                                    <option value="5">Compuesta (Campos definidos)</option>
                                </select>
                            </div>

                            <!-- Opciones Dinámicas -->
                            <div id="divOptions" style="display:none; border: 1px solid #ccc; padding: 10px; border-radius: 5px; background: #f9f9f9;">
                                <label>Opciones de Respuesta</label>
                                <div id="containerOptions"></div>
                                <div class="text-right mt-2">
                                    <button type="button" class="btn btn-sm btn-info" onclick="fntAddOption();"><i class="fas fa-plus"></i> Agregar Opción</button>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Agregar</button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Preguntas (Cards) -->
                    <div class="col-md-8">
                        <div id="containerQuestionsList" style="max-height: 500px; overflow-y: auto;">
                            <!-- Cargado por AJAX -->
                            <div class="text-center p-5 text-muted">Cargando preguntas...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php footerAdmin($data); ?>