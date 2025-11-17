<?php

class PqrsController
{
    public $addressmap;
    public $lat;

    /* Creacion de Marcas */
    public function create()
    {
        //echo '<pre>'; print_r($_POST); echo '</pre>';return;
        if (isset($_POST["name"])) {
            /*             echo '<script>
				matPreloader("on");
				fncSweetAlert("loading", "Loading...", "");
			</script>'; */

            /* Validamos la sintaxis de los campos */
            if (
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["name"])
                /*  &&
                preg_match('/^[.a-zA-Z0-9_]+([.][.a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["email"]) &&
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["address"]) &&
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["message"]) */
            ) {

                /* Capturo datos de la ubicacion de la app*/
                $url = "relations?rel=settings,departments,municipalities&type=setting,department,municipality&linkTo=id_setting&equalTo=1&token=" . $_SESSION["user"]->token_user . "&table=users&suffix=user";
                $data = "";
                $method = "GET";
                $fields = array();
                $settings = CurlController::request($url, $method, $fields);
                //echo '<pre>'; print_r($settings); echo '</pre>';exit;
                $namedpto =  $settings->results[0]->name_department;
                $namemuni =  $settings->results[0]->name_municipality;

                /* Agrupamos la información */
                $data = array(
                    "name_pqr" => $nombre,
                    "email_pqr" => $email,
                    "address_pqr" => $address,
                    "message_pqr" => $message,
                    "latitude_pqr" => $latitud,
                    "longitude_pqr" => $longitud,
                    "name_address_pqr" => $newdireccion,
                    "status_pqr" => 'Pending',
                    "date_created_pqr" => date("Y-m-d")
                );


                $url = "pqrs?token=" . $_SESSION["user"]->token_user . "&table=users&suffix=user";
                $method = "POST";
                $fields = $data;
                $response = CurlController::request($url, $method, $fields);

                /* Respuesta de la API */
                if ($response->status == 200) {
                    echo '<script>
					fncFormatInputs();
					matPreloader("off");
					fncSweetAlert("close", "", "");
					fncSweetAlert("success", "Registro grabado correctamente", "");
				</script>';
                }
            } else {
                echo '<script>
					fncFormatInputs();
					matPreloader("off");
					fncSweetAlert("close", "", "");
					fncNotie(3, "Error de sintaxys en los campos");
				</script>';
            }
        }
    }


    /* Asignar Cuadrilla */
    public function asign($id)
    {
        if (isset($_POST["idPqr"])) {

            echo '<script>
					matPreloader("on");
					fncSweetAlert("loading", "Loading...", "");
				</script>';

            if ($id == $_POST["idPqr"]) {
                $select = "id_pqr";
                $url = "pqrs?select=" . $select . "&linkTo=id_pqr&equalTo=" . $id;
                $method = "GET";
                $fields = array();
                $response = CurlController::request($url, $method, $fields);

                if ($response->status == 200) {

                    /* Agrupamos la información */
                    $data = "dateasing_pqr=" . $_POST["dateasign"] .
                        "&id_user_pqr=" . $_POST["username"] .
                        "&status_pqr=" . "Assign";

                    /* Solicitud a la API */
                    $url = "pqrs?id=" . $id . "&nameId=id_pqr&token=" . $_SESSION["user"]->token_user . "&table=users&suffix=user";

                    $method = "PUT";
                    $fields = $data;
                    $response = CurlController::request($url, $method, $fields);

                    /* Respuesta de la API */
                    if ($response->status == 200) {
                        echo '<script>
                        			fncFormatInputs();
									matPreloader("off");
									fncSweetAlert("close", "", "");
									fncSweetAlert("success", "Registro actualizado correctamente");
							</script>';
                    } else {
                        echo '<script>
									fncFormatInputs();
									matPreloader("off");
									fncSweetAlert("close", "", "");
									fncNotie(3, "Error al editar el registro");
								</script>';
                    }
                } else {
                    echo '<script>
							fncFormatInputs();
							matPreloader("off");
							fncSweetAlert("close", "", "");
							fncNotie(3, "Error editing the registry");
						</script>';
                }
            } else {
                echo '<script>
						fncFormatInputs();
						matPreloader("off");
						fncSweetAlert("close", "", "");
						fncNotie(3, "Error editing the registry");
				</script>';
            }
        }
    }

    /* Asignar Cuadrilla */
    public function solved($id)
    {
        //echo '<pre>'; print_r($_POST); echo '</pre>';
        if (isset($_POST["idPqr"])) {
            echo '<script>
                        matPreloader("on");
                        fncSweetAlert("loading", "Loading...", "");
                    </script>';

            if ($id == $_POST["idPqr"]) {
                $select = "id_pqr";
                $url = "pqrs?select=" . $select . "&linkTo=id_pqr&equalTo=" . $id;
                $method = "GET";
                $fields = array();
                $response = CurlController::request($url, $method, $fields);

                if ($response->status == 200) {

                    /* Agrupamos la información */
                    $data = "datesolved_pqr=" . $_POST["datesolved"] .
                        "&solution_pqr=" . $_POST["solution"] .
                        "&status_pqr=" . "Success" .
                        "&date_updated_pqr=" . date("Y-m-d");

                    /* Solicitud a la API */
                    $url = "pqrs?id=" . $id . "&nameId=id_pqr&token=" . $_SESSION["user"]->token_user . "&table=users&suffix=user";

                    $method = "PUT";
                    $fields = $data;
                    $response = CurlController::request($url, $method, $fields);

                    /* Respuesta de la API */
                    if ($response->status == 200) {
                        echo '<script>
                                        fncFormatInputs();
                                        matPreloader("off");
                                        fncSweetAlert("close", "", "");
                                        fncSweetAlert("success", "Registro actualizado correctamente", "/setpqrs");
                                </script>';
                    } else {
                        echo '<script>
                                        fncFormatInputs();
                                        matPreloader("off");
                                        fncSweetAlert("close", "", "");
                                        fncNotie(3, "Error al editar el registro");
                                    </script>';
                    }
                } else {
                    echo '<script>
                                fncFormatInputs();
                                matPreloader("off");
                                fncSweetAlert("close", "", "");
                                fncNotie(3, "Error editing the registry");
                            </script>';
                }
            } else {
                echo '<script>
                            fncFormatInputs();
                            matPreloader("off");
                            fncSweetAlert("close", "", "");
                            fncNotie(3, "Error editing the registry");
                    </script>';
            }
        }
    }

    /* Creacion de Marcas */
    public function create_ext()
    {
        if (isset($_POST["except"])) {
            echo '<script>
                    matPreloader("on");
                    fncSweetAlert("loading", "Loading...", "");
                  </script>';

            /* Validamos la sintaxis de los campos */
            if (
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["name"])
                /*  &&
                preg_match('/^[.a-zA-Z0-9_]+([.][.a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["email"]) &&
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["address"]) &&
                preg_match('/^[0-9A-Za-zñÑáéíóú ]{1,}$/', $_POST["message"]) */
            ) {

                /* Agrupamos la información */
                $data = array(
                    "name_pqr" => $_POST["name"],
                    "email_pqr" => $_POST["email"],
                    "phone_pqr" => $_POST["phone"],
                    "message_pqr" => $_POST["message"],
                    "date_created_pqr" => date("Y-m-d")
                );

                $url = "pqrs?token=no&except=" . $_POST["except_field"];
                $method = "POST";
                $fields = $data;
                $response = CurlController::request($url, $method, $fields);

                /* Respuesta de la API */
                if ($response->status == 200) {
                    echo '<script>
					fncFormatInputs();
					matPreloader("off");
					fncSweetAlert("close", "", "");
					fncSweetAlert("success", "Registro grabado correctamente", "");
				</script>';
                }
            } else {
                echo '<script>
					fncFormatInputs();
					matPreloader("off");
					fncSweetAlert("close", "", "");
					fncNotie(3, "Error de sintaxys en los campos");
				</script>';
            }
        }
    }
}
