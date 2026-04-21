<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/KpiModel.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

header('Content-Type: application/json; charset=utf-8');

try {
	$kpi = new KpiModel();
	echo json_encode([
		'ok' => true,
		'summary' => $kpi->summary(),
		'charts' => $kpi->charts(),
		'connection' => db_selected_meta()['label'],
	]);
} catch (Throwable $error) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'message' => 'No se pudieron cargar KPIs']);
}