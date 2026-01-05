<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-building"></i> <?= $data['page_title'] ?>
                <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nuevo</button>
            </h1>
            <p>Administración de Centros de Votación</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/centros"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableCentros">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Municipio</th>
                                    <th>Dirección</th>
                                    <th>Población</th>
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
<div class="modal fade" id="modalFormCentro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Centro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCentro" name="formCentro" class="form-horizontal">
                    <input type="hidden" id="idCentro" name="idCentro" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtNombre">Nombre Centro</label>
                            <input type="text" class="form-control" id="txtNombre" name="txtNombre" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtTelefono">Teléfono</label>
                            <input type="text" class="form-control" id="txtTelefono" name="txtTelefono" required="" onkeypress="return controlTag(event)">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtEmail">Email</label>
                            <input type="email" class="form-control" id="txtEmail" name="txtEmail" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtPoblacion">Población (Cantidad)</label>
                            <input type="number" class="form-control" id="txtPoblacion" name="txtPoblacion" required="">
                        </div>
                    </div>
                    <div class="form-row">
                         <div class="form-group col-md-6">
                            <label for="listDpto">Departamento</label>
                            <select class="form-control" data-live-search="true" id="listDpto" name="listDpto" required>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listMuni">Municipio</label>
                            <select class="form-control" data-live-search="true" id="listMuni" name="listMuni" required>
                            </select>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="txtDireccion">Dirección</label>
                         <textarea class="form-control" id="txtDireccion" name="txtDireccion" rows="2" required=""></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listStatus">Estado</label>
                            <select class="form-control selectpicker" id="listStatus" name="listStatus" required>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
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
<?php footerAdmin($data); ?>