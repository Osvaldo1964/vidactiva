<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-edit"></i> <?= $data['page_title'] ?></h1>
            <p>Diligenciar Encuestas</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/registro"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <!-- 1. Sección de Selección (Separada y Limpia) -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="form-group row align-items-center mb-0">
                        <label class="col-md-3 col-lg-2 col-form-label font-weight-bold text-right">Seleccionar Encuesta:</label>
                        <div class="col-md-6 col-lg-4">
                            <select class="form-control" id="listSurveys" name="listSurveys" onchange="fntLoadForm();" required>
                                <option value="">Seleccione una encuesta...</option>
                                <!-- Options loaded via JS -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Elija una opción para cargar el formulario.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Formulario de Preguntas (Centrado y Estilo Hoja) -->
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="tile" id="containerFormInfo" style="display:none;"> <!-- Oculto inicialmente -->
                <!-- Encabezado Visual del Formulario -->
                <h3 class="tile-title text-center text-primary" id="surveyTitle">Formulario de Registro</h3>
                <div class="tile-body">
                    <form id="formRegistro" name="formRegistro">
                        <!-- El input select está arriba, pero necesitamos enviar su valor. 
                             Podemos dejarlo arriba maneje todo o tener un hidden aquí si fuera necesario.
                             El JS actual lee listSurveys directamente, así que no hay problema. -->

                        <div id="containerForm">
                            <div class="text-center p-5"><i class="fas fa-spinner fa-spin"></i> Cargando formulario...</div>
                        </div>

                        <div id="divActions" class="mt-4 text-center border-top pt-4" style="display:none;">
                            <button id="btnSubmit" class="btn btn-primary btn-lg px-5 shadow-sm" type="submit">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Guardar Respuestas
                            </button>
                            <button class="btn btn-secondary btn-lg px-4 shadow-sm ml-2" type="button" onclick="fntResetForm();">
                                <i class="fa fa-fw fa-lg fa-times-circle"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>