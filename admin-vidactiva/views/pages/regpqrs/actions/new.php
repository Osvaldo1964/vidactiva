<div class="card card-dark card-outlin<div class=" card card-dark card-outline">
    <form method="post" class="needs-validation" novalidate enctype="multipart/form-data">
        <input type="hidden" value="except" name="except">
        <input type="hidden" value="id_pqr" name="except_field">
        <input type="hidden" value="0" name="edReg" id="edReg">
        <div class="card-header">
            <div class="form-row col-md-12">
                <!-- Departamentos -->
                <div class="form-group col-md-3">
                    <label>Departamento</label>
                    <select class="form-select dpto_support" id="dpto_support" name="dpto_support" required>
                    </select>
                </div>

                <!-- Municipios -->
                <div class="form-group col-md-3">
                    <label>Municipio</label>
                    <select class="form-select muni_support" id="muni_support" name="muni_support" required>
                    </select>
                </div>
            </div>
            <div class="form-row col-md-12">
                <!-- Nombre  -->
                <div class="form-group col-md-12">
                    <div class="form-group">
                        <label>Apellidos y Nombre</label>
                        <input type="text" class="form-control" pattern='[a-zA-Z0-9_ ]{1,}' name="name" required>
                        <div class="valid-feedback">Valid.</div>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </div>
                <!-- Correo electrónico -->
                <div class="form-group col-md-8">
                    <label>Email</label>
                    <input type="email" class="form-control" pattern="[.a-zA-Z0-9_]+([.][.a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}" name="email" required>
                    <div class="valid-feedback">Valid.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                </div>
                <!-- Teléfono -->
                <div class="form-group col-md-4">
                    <label>Teléfono</label>
                    <input type="number" class="form-control numDocumento" onchange="validateJS(event,'num')" name="phone" required>
                    <div class="valid-feedback">Valid.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                </div>
                <!-- Descripcion de la Falla -->
                <div class="form-group col-md-12">
                    <div class="form-group">
                        <label>Descripción </label>
                        <textarea class="form-control" cols="65" rows="3" placeholder="Detalle ..." name="message"></textarea>
                        <div class="valid-feedback">Valid.</div>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </div>
            </div>

            <?php
            require_once "controllers/pqrs.controller.php";
            $create = new PqrsController();
            $create->create_ext();
            ?>
        </div>
        <div class="card-footer">
            <div class="col-md-8 offset-md-2">
                <div class="form-group submtit">
                    <a href="/pqrs" class="btn btn-light border text-left">Back</a>
                    <button type="submit" class="btn bg-dark float-right saveBtn">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    //Verifico departamentos al cargar la forma
    (function() {
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Trigger ejecutado: DOM listo!");
            supDptos();
        });
    })();
</script>