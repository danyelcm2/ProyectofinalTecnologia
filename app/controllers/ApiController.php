<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/KpiModel.php';

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

}
