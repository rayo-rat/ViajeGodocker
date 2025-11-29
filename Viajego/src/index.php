<?php
// CLAVE: Inicia la sesión y carga la función de conexión refactorizada
session_start(); 
require_once 'db_connect.php'; 

// --- Lógica de redirección ---
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : '';

// URL a la que se redirige si el usuario NO ha iniciado sesión
$booking_url = $is_logged_in ? '#' : '/registro.php'; 

// --- Lógica para obtener datos de las diferentes bases de datos ---
$vuelos = [];
$hoteles = [];

// Paquetes simulados que replican EXACTAMENTE la imagen
$paquetes_simulados = [
    [
        'titulo' => 'Guadalajara', 
        'descripcion' => 'Hospeda 5 días / 4 noches en el hotel Gala.', 
        'precio' => '2,300.00', 
        'tag' => 'México Mágico Tours', 
        'img_src' => 'https://via.placeholder.com/400x200/FFD700/FFFFFF?text=Guadalajara',
        'transporte' => 'Aéreo', 'habitacion' => 'Sencilla', 'plan' => 'Desayuno'
    ],
    [
        'titulo' => 'Los Cabos', 
        'descripcion' => 'Hospeda 3 días / 2 noches en el hotel Maravilla.', 
        'precio' => '2,310.00', 
        'tag' => 'Caribe & Sol Travel', 
        'img_src' => 'https://via.placeholder.com/400x200/87CEEB/FFFFFF?text=Los+Cabos',
        'transporte' => 'Aéreo', 'habitacion' => 'Doble', 'plan' => 'Todo Incluido'
    ],
    [
        'titulo' => 'Los Cabos', 
        'descripcion' => 'Hospeda 3 días / 2 noches en el hotel Oasis.', 
        'precio' => '2,390.00', 
        'tag' => 'Caribe & Sol Travel', 
        'img_src' => 'https://via.placeholder.com/400x200/ADD8E6/FFFFFF?text=Los+Cabos+Oasis',
        'transporte' => 'Aéreo', 'habitacion' => 'Doble', 'plan' => 'Todo Incluido'
    ],
    [
        'titulo' => 'La Casa', 
        'descripcion' => 'Hospeda 5 días / 4 noches en un hotel boutique.', 
        'precio' => '2,350.00', 
        'tag' => 'Estilo Libre Viajes', 
        'img_src' => 'https://via.placeholder.com/400x200/CD5C5C/FFFFFF?text=La+Casa+Boutique',
        'transporte' => 'Aéreo', 'habitacion' => 'Sencilla', 'plan' => 'Desayuno'
    ],
];


try {
    // 1. OBTENER VUELOS (Lógica de conexión y datos simulados)
    $pdo_vuelos = connectVuelosDB();
    if ($pdo_vuelos) {
        $sql_vuelos = "SELECT codigo_vuelo, origen_iata, destino_iata, fecha_salida, precio FROM vuelos LIMIT 5";
        $vuelos_data = $pdo_vuelos->query($sql_vuelos)->fetchAll();

        $vuelos = array_map(function($v) {
            $vuelos_mapping = [
                'GO101' => ['Aeromexico (AM305)', 'Ciudad de México → Oaxaca'],
                'GO102' => ['Aeromexico (AM500)', 'Ciudad de México → Cancún'],
                'GO205' => ['Volaris (Y4-100)', 'Guadalajara → Cancún'],
                'GO300' => ['VivaAerobus (VB202)', 'Monterrey → Los Cabos'],
            ];
            
            $info = $vuelos_mapping[$v['codigo_vuelo']] ?? ['Volaris (Y4-550)', 'Cancún → Ciudad de México']; 
            
            $v['aerolinea_display'] = $info[0];
            $v['ruta_display'] = $info[1];
            $v['tipo_viaje'] = (rand(0, 1) == 0) ? 'Round-Trip' : 'One-Way'; 
            return $v;
        }, $vuelos_data);
    }

    // 2. OBTENER HOTELES (Lógica de conexión y datos simulados)
    $pdo_hoteles = connectHotelesDB();
    if ($pdo_hoteles) {
        $sql_hoteles = "SELECT nombre, ciudad, estrellas, precio_noche FROM hoteles LIMIT 6";
        $hoteles = $pdo_hoteles->query($sql_hoteles)->fetchAll();
    }

} catch (\Exception $e) {
    // Los errores de conexión ya se muestran en connectDatabase()
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ViajeGO | Agencia de Viajes</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    
    <div class="top-bar">
        <div class="container d-flex justify-content-end align-items-center">
            <a href="/login.php" class="text-decoration-none text-secondary">Ingresar</a>
            <a href="/registro.php" class="btn btn-sm btn-primary ms-2 rounded-pill btn-register">Registro</a>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-paper-plane"></i> ViajeGO
            </a>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#paquete-personalizado">Reservar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#catalogo-nacional">Servicios</a>
                    </li>
                    </ul>
                
                <?php if ($is_logged_in): ?>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <span class="nav-link text-success fw-bold">Hola, <?php echo $user_name; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-login" href="/dashboard.php">Panel</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-danger btn-register" href="/logout.php">Cerrar Sesión</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <h1>Explora México con ViajeGO</h1>
                    <p class="lead">Playas, Ciudades Coloniales y Aventura</p>
                </div>
                
                <div class="col-lg-8 hero-search-container">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar destino (Ej: Cancún, Guadalajara...)" aria-label="Buscar destino">
                        <button class="btn btn-hero-search" type="button" id="button-addon2">
                            <i class="fas fa-search"></i> Buscar Ofertas
                        </button>
                    </div>
                </div>
                <div class="col-lg-10 mt-5">
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="#paquete-personalizado" class="btn btn-hero-primary btn-lg">Armar Viaje Personalizado</a>
                        <a href="#catalogo-nacional" class="btn btn-hero-secondary btn-lg">Ver Catálogo Nacional</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5 pt-5" id="catalogo-nacional">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                
                <h2 class="fw-bold mb-1">Catálogo Nacional</h2>
                <p class="text-secondary mb-5">Reserva paquetes completos o servicios individuales.</p>

                <ul class="nav nav-pills justify-content-center gap-3 mb-5 nav-pills-custom" id="catalogoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="paquetes-tab" data-bs-toggle="pill" data-bs-target="#paquetes-content" type="button" role="tab" aria-controls="paquetes-content" aria-selected="true">
                            <i class="fas fa-box-open"></i> Paquetes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hoteles-tab" data-bs-toggle="pill" data-bs-target="#hoteles-content" type="button" role="tab" aria-controls="hoteles-content" aria-selected="false">
                            <i class="fas fa-hotel"></i> Hoteles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="vuelos-tab" data-bs-toggle="pill" data-bs-target="#vuelos-content" type="button" role="tab" aria-controls="vuelos-content" aria-selected="false">
                            <i class="fas fa-plane"></i> Vuelos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="autobuses-tab" data-bs-toggle="pill" data-bs-target="#autobuses-content" type="button" role="tab" aria-controls="autobuses-content" aria-selected="false">
                            <i class="fas fa-bus-alt"></i> Autobuses
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="paquetes-content" role="tabpanel" aria-labelledby="paquetes-tab">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($paquetes_simulados as $paquete): ?>
                                <div class="col">
                                    <div class="card shadow-sm paquete-card">
                                        <img src="<?php echo $paquete['img_src']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($paquete['titulo']); ?>">
                                        
                                        <div class="card-body text-start">
                                            <p class="small-tag"><?php echo htmlspecialchars($paquete['tag']); ?></p>
                                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($paquete['titulo']); ?></h5>
                                            <p class="card-text-desc"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
                                        </div>
                                        
                                        <div class="card-footer card-footer-custom">
                                            <span class="details-text">Transporte por defecto: <strong><?php echo htmlspecialchars($paquete['transporte']); ?></strong></span>
                                            <span class="details-text">Habitación: <strong><?php echo htmlspecialchars($paquete['habitacion']); ?></strong></span>
                                            <span class="details-text">Plan por defecto: <strong><?php echo htmlspecialchars($paquete['plan']); ?></strong></span>
                                            <span class="price">$<?php echo htmlspecialchars($paquete['precio']); ?> MXN</span>
                                            <a href="<?php echo $booking_url; ?>" class="btn btn-primary-custom btn-custom-card">Reservar</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="hoteles-content" role="tabpanel" aria-labelledby="hoteles-tab">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php if (!empty($hoteles)): ?>
                                <?php foreach ($hoteles as $hotel): ?>
                                    <div class="col">
                                        <div class="card shadow-sm paquete-card">
                                            <div class="icon-placeholder">
                                                 <i class="fas fa-hotel"></i>
                                            </div>
                                            <div class="card-body text-start">
                                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($hotel['nombre']); ?></h5>
                                                <p class="card-text text-secondary small mb-1"><?php echo htmlspecialchars($hotel['ciudad']); ?></p>
                                                <p class="card-text small text-muted"><?php echo str_repeat('⭐', $hotel['estrellas']); ?></p>
                                            </div>
                                            <div class="card-footer card-footer-custom">
                                                <span class="price">$<?php echo number_format($hotel['precio_noche'], 2); ?></span>
                                                <span class="details-text">/ noche base</span>
                                                <a href="<?php echo $booking_url; ?>" class="btn btn-primary-custom btn-custom-card">Reservar Hotel</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center w-100">No hay hoteles disponibles actualmente o la conexión falló.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="vuelos-content" role="tabpanel" aria-labelledby="vuelos-tab">
                         <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php if (!empty($vuelos)): ?>
                                <?php foreach ($vuelos as $vuelo): ?>
                                    <div class="col">
                                        <div class="card shadow-sm paquete-card">
                                            <div class="icon-placeholder">
                                                <i class="fas fa-plane"></i>
                                            </div>
                                            <div class="card-body text-start">
                                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($vuelo['aerolinea_display']); ?></h5>
                                                <p class="card-text text-secondary small mb-1"><?php echo htmlspecialchars($vuelo['ruta_display']); ?></p>
                                                <p class="card-text small text-muted">Salida: <?php echo date('d/m H:i', strtotime($vuelo['fecha_salida'])); ?></p>
                                                <p class="card-text small text-muted">Tipo de Viaje: <?php echo htmlspecialchars($vuelo['tipo_viaje']); ?></p>
                                            </div>
                                            <div class="card-footer card-footer-custom">
                                                <span class="price">$<?php echo number_format($vuelo['precio'], 2); ?></span>
                                                <a href="<?php echo $booking_url; ?>" class="btn btn-primary-custom btn-custom-card">Comprar Vuelo</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center w-100">No hay vuelos disponibles actualmente o la conexión falló.</p>
                            <?php endif; ?>
                         </div>
                    </div>
                    
                    <div class="tab-pane fade" id="autobuses-content" role="tabpanel" aria-labelledby="autobuses-tab">
                        <h3 class="fw-bold" style="color: var(--color-acento-rojo);">🚌 ¡Próximamente Autobuses!</h3>
                        <p class="text-secondary">Pronto podrás reservar rutas nacionales con gran comodidad.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <hr class="mt-5">
    
    <div class="container mt-5 pt-5" id="paquete-personalizado">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4" style="color: var(--color-texto-principal);">Arma tu Viaje Soñado</h2>
            </div>
            <div class="col-lg-10">
                <div class="form-paquete-personalizado">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="destino_paquete" class="form-label small text-muted">1. Selecciona Paquete Base (Solo ida y Vuelta)</label>
                                <select id="destino_paquete" class="form-select">
                                    <option selected>— Elige un destino —</option>
                                    <option>Cancún</option>
                                    <option>Guadalajara</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipo_transporte" class="form-label small text-muted">2. Tipo de Transporte</label>
                                <select id="tipo_transporte" class="form-select">
                                    <option selected>— Elige un tipo —</option>
                                    <option>Aéreo</option>
                                    <option>Terrestre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipo_habitacion" class="form-label small text-muted">3. Tipo de Habitación</label>
                                <select id="tipo_habitacion" class="form-select">
                                    <option selected>Habitación Sencilla (x1.00)</option>
                                    <option>Doble (x1.50)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <label for="plan_alimentos" class="form-label small text-muted">4. Plan de Alimentos</label>
                                <select id="plan_alimentos" class="form-select">
                                    <option selected>Solo Alojamiento (+$0.00 p/noche)</option>
                                    <option>Desayuno (+$150 p/noche)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label small text-muted">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="fecha_inicio">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label small text-muted">Fecha Fin (Mín 1 Noche):</label>
                                <input type="date" class="form-control" id="fecha_fin">
                            </div>
                            <div class="col-md-3">
                                <label for="adultos" class="form-label small text-muted">Adultos:</label>
                                <input type="number" class="form-control" id="adultos" value="2" min="1">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?php echo $booking_url; ?>" class="btn btn-primary-custom btn-lg">Ir a Pagar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container text-center">
            <p class="mb-0">© <?php echo date("Y"); ?> ViajeGO | Desarrollado por <strong>Misael</strong>.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>