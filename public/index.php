<?php require_once __DIR__.'/includes/header.php'; ?>
<section class="position-relative overflow-hidden rounded-3 mb-4" style="min-height:240px;background:#f8f9fa;">
	<div class="position-relative p-4 p-lg-5">
		<h1 class="display-6 fw-semibold">Encontrá y destacá servicios para mascotas en tu zona</h1>
		<p class="lead mb-4">Buscá veterinarios, paseadores, peluquerías y más. Si sos prestador, potenciá tu visibilidad con nuestro mapa y planes Pro/Premium.</p>
		<div class="d-flex gap-2 flex-wrap">
			<a class="btn btn-primary btn-lg" href="<?= APP_URL ?>/servicios.php">Buscar servicios</a>
			<a class="btn btn-outline-primary btn-lg" href="<?= APP_URL ?>/prestadores.php">Soy prestador</a>
		</div>
	</div>
	</section>

<section class="mb-5">
	<div class="row g-4 align-items-stretch">
		<div class="col-12 col-lg-6">
			<div class="border rounded-3 h-100 p-4 p-lg-5 bg-light">
				<h2 class="h4 mb-2">Para familias y pacientes (plan Free)</h2>
				<p class="mb-3">Creá tu cuenta gratis y organizá todo lo de tus mascotas en un solo lugar.</p>
				<ul class="mb-4">
					<li>Agenda de vacunas por mascota</li>
					<li>Recordatorios automáticos</li>
					<li>Multi-mascota</li>
					<li>Buscar y filtrar prestadores por zona</li>
					<li>Historial básico de atenciones</li>
				</ul>
				<div class="d-flex gap-2 flex-wrap">
					<a href="<?= APP_URL ?>/servicios.php" class="btn btn-primary btn-lg">Explorar servicios</a>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-6">
			<div class="border rounded-3 h-100 p-4 p-lg-5">
				<h2 class="h4 mb-2">Para prestadores</h2>
				<p class="mb-3">Mostrá tu trabajo, aparecé en el mapa y recibí más consultas.</p>
				<div class="row g-3">
					<div class="col-12 col-md-4">
						<div class="card h-100">
							<div class="card-body">
								<h3 class="h6">Free</h3>
								<ul class="small mb-3">
									<li>Ficha básica en listados</li>
									<li>Contacto por WhatsApp y email</li>
								</ul>
								<a class="btn btn-outline-secondary btn-sm" href="<?= APP_URL ?>/prestadores.php#planes">Ver detalles</a>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4">
						<div class="card h-100 border-primary">
							<div class="card-body">
								<h3 class="h6">Pro</h3>
								<ul class="small mb-3">
									<li>Prioridad en búsquedas</li>
									<li>Hasta 10 fotos (álbum)</li>
									<li>Visibilidad en el mapa</li>
								</ul>
								<a class="btn btn-primary btn-sm" href="<?= APP_URL ?>/prestadores.php#planes">Quiero ser Pro</a>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4">
						<div class="card h-100 border-warning">
							<div class="card-body">
								<h3 class="h6">Premium</h3>
								<ul class="small mb-3">
									<li>Todas las de Pro</li>
									<li>Máxima prioridad</li>
									<li>Áreas destacadas</li>
								</ul>
								<a class="btn btn-outline-warning btn-sm" href="<?= APP_URL ?>/prestadores.php#planes">Quiero Premium</a>
							</div>
						</div>
					</div>
				</div>
				<p class="text-muted small mt-3 mb-0">Podés empezar Free y mejorar cuando quieras.</p>
			</div>
		</div>
	</div>
</section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
