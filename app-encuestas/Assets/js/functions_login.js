$('.login-content [data-toggle="flip"]').click(function () {
	$('.login-box').toggleClass('flipped');
	return false;
});

var divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function () {

	// -----------------------------------------------------------------------
	// LOGIN FORM
	// -----------------------------------------------------------------------
	if (document.querySelector("#formLogin")) {
		let formLogin = document.querySelector("#formLogin");
		formLogin.onsubmit = async function (e) {
			e.preventDefault();

			let strEmail = document.querySelector('#txtEmail').value;
			let strPassword = document.querySelector('#txtPassword').value;

			if (strEmail == "" || strPassword == "") {
				swal("Por favor", "Escribe usuario y contraseña.", "error");
				return false;
			}

			divLoading.style.display = "flex";

			try {
				// 1. Petición a la API (API remota o local)
				let formData = new FormData(formLogin);
				let urlApi = `${BASE_URL_API}/login/loginUser`;

				let response = await fetch(urlApi, {
					method: 'POST',
					body: formData
				});

				let objData = await response.json();

				if (objData.status) {
					// Guardar datos en LocalStorage
					localStorage.setItem('idUser', objData.auth.id_usuario);
					localStorage.setItem('userEmail', objData.auth.email_usuario);
					localStorage.setItem('userToken', objData.auth.access_token);
					localStorage.setItem('userRol', objData.auth.rol_usuario);
					localStorage.setItem('login', true);

					// 2. Crear sesión en servidor Frontend (PHP)
					let urlSession = `${BASE_URL}/login/crearSesion`;
					let sessionResponse = await fetch(urlSession, {
						method: 'POST',
						body: JSON.stringify(objData.auth),
						headers: {
							'Content-Type': 'application/json'
						}
					});

					if (sessionResponse.ok) {
						window.location = `${BASE_URL}/dashboard`;
					} else {
						swal("Atención", "Error al crear la sesión local.", "error");
					}

				} else {
					swal("Atención", objData.msg, "error");
					document.querySelector('#txtPassword').value = "";
				}

			} catch (error) {
				console.error("Error Login:", error);
				swal("Atención", "Error de conexión con el servidor.", "error");
			} finally {
				divLoading.style.display = "none";
			}
		}

		// Check Logout param
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('logout') === 'true') {
			localStorage.clear();
			window.history.replaceState({}, document.title, "/login");
			console.log("Sesión y LocalStorage limpiados correctamente.");
		}
	}

	// -----------------------------------------------------------------------
	// RESET PASSWORD FORM
	// -----------------------------------------------------------------------
	if (document.querySelector("#formRecetPass")) {
		let formRecetPass = document.querySelector("#formRecetPass");
		formRecetPass.onsubmit = async function (e) {
			e.preventDefault();
			let strEmail = document.querySelector('#txtEmailReset').value;
			if (strEmail == "") {
				swal("Por favor", "Escribe tu correo electrónico.", "error");
				return false;
			}

			divLoading.style.display = "flex";
			try {
				let formData = new FormData(formRecetPass);
				let urlApi = `${BASE_URL_API}/login/resetPass`;

				let response = await fetch(urlApi, {
					method: 'POST',
					body: formData
				});
				let objData = await response.json();

				if (objData.status) {
					swal({
						title: "",
						text: objData.msg,
						type: "success",
						confirmButtonText: "Aceptar",
						closeOnConfirm: false,
					}, function (isConfirm) {
						if (isConfirm) {
							window.location = BASE_URL;
						}
					});
				} else {
					swal("Atención", objData.msg, "error");
				}
			} catch (error) {
				console.error(error);
				swal("Atención", "Error en el proceso", "error");
			} finally {
				divLoading.style.display = "none";
			}
		}
	}

	// -----------------------------------------------------------------------
	// CHANGE PASSWORD FORM (If exists)
	// -----------------------------------------------------------------------
	if (document.querySelector("#formCambiarPass")) {
		let formCambiarPass = document.querySelector("#formCambiarPass");
		formCambiarPass.onsubmit = async function (e) {
			e.preventDefault();

			let strPassword = document.querySelector('#txtPassword').value;
			let strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;

			if (strPassword == "" || strPasswordConfirm == "") {
				swal("Por favor", "Escribe la nueva contraseña.", "error");
				return false;
			}
			if (strPassword.length < 5) {
				swal("Atención", "La contraseña debe tener un mínimo de 5 caracteres.", "info");
				return false;
			}
			if (strPassword != strPasswordConfirm) {
				swal("Atención", "Las contraseñas no son iguales.", "error");
				return false;
			}

			divLoading.style.display = "flex";
			try {
				let formData = new FormData(formCambiarPass);
				// Note: using BASE_URL (local) or BASE_URL_API depending on implementation
				// Original code used 'base_url + /Login/setPassword' which suggests local controller
				// We use BASE_URL assuming it's the global var for App URL
				let url = `${BASE_URL}/Login/setPassword`;

				let response = await fetch(url, {
					method: 'POST',
					body: formData
				});
				let objData = await response.json();

				if (objData.status) {
					swal({
						title: "",
						text: objData.msg,
						type: "success",
						confirmButtonText: "Iniciar sesión",
						closeOnConfirm: false,
					}, function (isConfirm) {
						if (isConfirm) {
							window.location = `${BASE_URL}/login`;
						}
					});
				} else {
					swal("Atención", objData.msg, "error");
				}
			} catch (error) {
				console.error(error);
				swal("Atención", "Error en el proceso", "error");
			} finally {
				divLoading.style.display = "none";
			}
		}
	}

}, false);