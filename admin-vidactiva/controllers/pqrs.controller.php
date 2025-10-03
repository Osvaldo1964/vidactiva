<?php

class PqrsController
{

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

                /* Verifico la direccion con google */
                $nombre = trim(TemplateController::capitalize($_POST["name"]));
                $email  = strtolower($_POST['email']);
                $address  = strtolower(($_POST['address'])) . ', Santa Marta Colombia';
                $message  = $_POST['message'];
                $coordenadas = $this->getGeocodeData($address);
                $latitud = $coordenadas[0];
                $longitud = $coordenadas[1];
                $newdireccion = $coordenadas[2];


                /* Agrupamos la información */
                $data = array(
                    "name_pqr" => $nombre,
                    "email_pqr" => $email,
                    "address_pqr" => $address,
                    "message_pqr" => $message,
                    "latitude_pqr" => $latitud,
                    "longitude_pqr" => $longitud,
                    "name_address_pqr" => $newdireccion,
                    "status_pqr" => 'Activo',
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
                    $data = "dateasign_pqr=" . $_POST["dateasign"] .
                        "&id_user_pqr=" . trim(TemplateController::capitalize($_POST["username"]));

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

    function getGeocodeData($address)
    {
        $address = urlencode($address);
        $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDDTJ5uq4WEhP4noQ6DKM7aFVUYwGabdu8";
        $geocodeResponseData = file_get_contents($googleMapUrl);
        $responseData = json_decode($geocodeResponseData, true);
        if ($responseData['status'] == 'OK') {
            $latitude = isset($responseData['results'][0]['geometry']['location']['lat']) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
            $longitude = isset($responseData['results'][0]['geometry']['location']['lng']) ? $responseData['results'][0]['geometry']['location']['lng'] : "";
            $formattedAddress = isset($responseData['results'][0]['formatted_address']) ? $responseData['results'][0]['formatted_address'] : "";
            if ($latitude && $longitude && $formattedAddress) {
                $geocodeData = array();
                array_push($geocodeData, $latitude, $longitude, $formattedAddress);
                return $geocodeData;
            } else {
                return false;
            }
        } else {
            echo "ERROR: {$responseData['status']}";
            return false;
        }
    }
}
