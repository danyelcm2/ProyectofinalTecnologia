<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/KpiModel.php';
require_once __DIR__ . '/../models/SchemaModel.php';

class ApiController
{
    public function kpis(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $kpi = new KpiModel();
            echo json_encode([
                'ok' => true,
                'summary' => $kpi->summary(),
                'charts' => $kpi->charts(),
                'connection' => db_selected_meta()['label'],
                'updatedAt' => date('Y-m-d H:i:s'),
            ]);
        } catch (InvalidArgumentException $error) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $error->getMessage()]);
        } catch (Throwable $error) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'No se pudieron cargar KPIs']);
        }
    }

    public function tables(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $schema = new SchemaModel();
            echo json_encode(['ok' => true, 'tables' => $schema->tables()]);
        } catch (Throwable $error) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'No se pudieron obtener tablas']);
        }
    }

    public function columns(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $table = (string) ($_GET['table'] ?? '');
            $schema = new SchemaModel();
            echo json_encode(['ok' => true, 'columns' => $schema->columns($table)]);
        } catch (Throwable $error) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Tabla invalida']);
        }
    }

    public function records(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $table = (string) ($_GET['table'] ?? '');
            $limit = (int) ($_GET['limit'] ?? 200);
            $schema = new SchemaModel();
            echo json_encode(['ok' => true, 'data' => $schema->records($table, $limit)]);
        } catch (Throwable $error) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'No se pudieron cargar los registros de la tabla']);
        }
    }

    public function insert(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'message' => 'Metodo no permitido']);
            return;
        }

        try {
            $table = (string) ($_POST['table'] ?? '');
            $payload = $_POST;
            unset($payload['table']);

            $schema = new SchemaModel();
            $schema->insert($table, $payload);

            echo json_encode(['ok' => true, 'message' => 'Registro insertado correctamente']);
        } catch (InvalidArgumentException $error) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $error->getMessage()]);
        } catch (Throwable $error) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'No fue posible insertar el registro']);
        }
    }
}
