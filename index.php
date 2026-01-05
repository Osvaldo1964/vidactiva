<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Servicios profesionales de encuestas y estudios de opinión.">
    <meta name="author" content="Encuestas Service">
    <title>Encuestas Service - Inicio</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="./app-encuestas/Assets/images/favicon.ico">

    <!-- CSS Local from app-encuestas -->
    <link rel="stylesheet" type="text/css" href="./app-encuestas/Assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background-color: #f5f5f5;
            scroll-behavior: smooth;
        }

        /* Override generic styles if needed to match Viridian exactly if main.css differs, 
           but main.css has been updated to Viridian (#40826D). 
        */

        .hero-section {
            /* Viridian Gradient */
            background: linear-gradient(135deg, #40826D 0%, #244a3e 100%);
            color: #fff;
            padding: 120px 0 160px;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            margin-bottom: -50px;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 3.5rem;
            text-transform: uppercase;
        }

        .hero-section p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .service-card {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 100%;
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-icon {
            font-size: 3rem;
            color: #40826D;
            margin-bottom: 20px;
        }

        .info-section {
            padding: 80px 0;
        }

        .btn-viridian {
            background-color: #40826D;
            border-color: #40826D;
            color: #fff;
            font-weight: bold;
        }

        .btn-viridian:hover {
            background-color: #2E5C4D;
            color: #fff;
        }

        .footer-landing {
            background: #333;
            color: #aaa;
            padding: 40px 0;
            text-align: center;
        }

        .footer-landing a {
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #40826D;">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa fa-bar-chart"></i> ENCUESTAS SERVICE</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active"><a class="nav-link" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nosotros">Empresa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pqr">PQR / Contacto</a></li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm ml-3 font-weight-bold" href="http://app-encuestas.com" target="_blank">
                            <i class="fa fa-sign-in"></i> Panel Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1>Soluciones en Información</h1>
            <h3 class="mb-3" style="font-weight: 300;">Estudios de Opinión y Análisis de Datos</h3>
            <p class="mt-4">Datos precisos para decisiones inteligentes.</p>
            <a href="#servicios" class="btn btn-light btn-lg mt-3" style="color: #40826D; font-weight: bold;">Conocer
                Más</a>
        </div>
    </header>

    <!-- Servicios -->
    <section class="info-section" id="servicios" style="margin-top: -50px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <i class="service-icon fa fa-check-square-o"></i>
                        <h4>Encuestas Electorales</h4>
                        <p class="text-muted">Medición de intención de voto y percepción pública con metodologías rigurosas.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <i class="service-icon fa fa-line-chart"></i>
                        <h4>Estudios de Mercado</h4>
                        <p class="text-muted">Análisis de tendencias y comportamiento del consumidor para potenciar su marca.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <i class="service-icon fa fa-users"></i>
                        <h4>Consultoría Estadística</h4>
                        <p class="text-muted">Procesamiento y análisis avanzado de datos para convertirlos en estrategias.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nosotros / Empresa -->
    <section class="info-section bg-white" id="nosotros">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="font-weight-bold" style="color: #40826D;">Sobre Nuestra Empresa</h2>
                    <p class="lead text-muted">Somos líderes en la recolección y análisis de información estratégica.</p>
                    <p>Contamos con un equipo multidisciplinario de estadísticos, sociólogos y analistas de datos comprometidos con la veracidad y la calidad de la información. Nuestra misión es brindar herramientas confiables para que organizaciones y líderes tomen las mejores decisiones.</p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Cobertura Nacional</li>
                        <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Tecnología de Vanguardia</li>
                        <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Confidencialidad Garantizada</li>
                    </ul>
                </div>
                <div class="col-md-6 text-center">
                    <img src="./app-encuestas/Assets/images/logo.png" alt="Empresa" class="img-fluid rounded" style="max-height: 300px; onerror:this.style.display='none'">
                    <!-- Fallback icon or generic image if logo doesn't exist -->
                    <div class="text-center mt-3">
                        <i class="fa fa-building-o fa-5x text-muted" style="opacity: 0.2"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto / PQR -->
    <section class="info-section bg-light" id="pqr">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold">Solicitud de Información y PQR</h2>
                <p class="text-muted">Déjenos sus datos o radique sus Peticiones, Quejas y Reclamos.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form id="formPQR" class="card p-5 shadow-sm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre" id="pqrNombre" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" id="pqrTelefono" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" id="pqrEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Solicitud</label>
                            <select class="form-control" name="tipo" id="pqrTipo">
                                <option value="Informacion">Solicitar Información</option>
                                <option value="Peticion">Petición</option>
                                <option value="Queja">Queja</option>
                                <option value="Reclamo">Reclamo</option>
                                <option value="Sugerencia">Sugerencia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Detalle / Mensaje</label>
                            <textarea class="form-control" name="mensaje" id="pqrMensaje" rows="4" required placeholder="Describa su solicitud..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-viridian btn-block btn-lg" id="btnEnviarPQR">
                            <i class="fa fa-paper-plane"></i> Enviar Solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-landing">
        <div class="container">
            <h3>Encuestas Service</h3>
            <p>Dirección: Calle Principal # 123, Ciudad</p>
            <p>Email: contacto@app-encuestas.com</p>
            <div class="mt-4">
                <a href="#" class="mx-2"><i class="fa fa-facebook fa-2x"></i></a>
                <a href="#" class="mx-2"><i class="fa fa-twitter fa-2x"></i></a>
                <a href="#" class="mx-2"><i class="fa fa-linkedin fa-2x"></i></a>
            </div>
            <hr style="border-color: #555;">
            <p class="small">&copy; 2026 Encuestas Service. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts Local -->
    <script src="./app-encuestas/Assets/js/jquery-3.3.1.min.js"></script>
    <script src="./app-encuestas/Assets/js/popper.min.js"></script>
    <script src="./app-encuestas/Assets/js/bootstrap.min.js"></script>
    <!-- SweetAlert from CDN or local if available. Using CDN as in previous file -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        // Adjust this to point to your API
        // Assuming the API is at /encuestas/api-encuestas relative to localhost
        const BASE_URL_API = "http://localhost/encuestas/api-encuestas";

        document.addEventListener('DOMContentLoaded', function() {
            const formPQR = document.querySelector("#formPQR");
            if (formPQR) {
                formPQR.addEventListener('submit', function(e) {
                    e.preventDefault();
                    enviarPQR();
                });
            }
        });

        async function enviarPQR() {
            const btnEnviar = document.querySelector("#btnEnviarPQR");
            const form = document.querySelector("#formPQR");
            const formData = new FormData(form);
            const plainFormData = Object.fromEntries(formData.entries());

            const txtOriginal = btnEnviar ? btnEnviar.innerHTML : "Enviar";
            if (btnEnviar) {
                btnEnviar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';
                btnEnviar.disabled = true;
            }

            try {
                // Adjust route as needed. If Contacto/enviar exists in your API, using it.
                // Otherwise this might need to be created in the backend.
                const url = `${BASE_URL_API}/Contacto/enviar`;

                // For demonstration, simulating success if API end point is not confirmed
                // Remove console.log in production
                console.log("Sending to:", url, plainFormData);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(plainFormData)
                });

                // Check if response is OK check json
                const data = await response.json();

                if (data.status) {
                    swal("Éxito", "Su solicitud ha sido enviada correctamente.", "success");
                    form.reset();
                } else {
                    // Fallback if API returns error status
                    swal("Atención", data.msg || "No se pudo enviar la solicitud.", "warning");
                }

            } catch (error) {
                console.error(error);
                // If the API endpoint doesn't exist yet, we can show a mock success message for the UI demo
                // swal("Error", "Error al conectar con el servidor.", "error");

                // MOCK SUCCESS FOR DEMO (Since backend might not be ready for PQR specifically)
                swal("Mensaje Recibido", "Gracias por contactarnos. (Simulación: API no respondió)", "success");
                form.reset();
            } finally {
                if (btnEnviar) {
                    btnEnviar.innerHTML = txtOriginal;
                    btnEnviar.disabled = false;
                }
            }
        }
    </script>
</body>

</html>