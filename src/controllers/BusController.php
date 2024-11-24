<?php

namespace App\controllers;

require_once __DIR__ . '/../../src/models/BusModel.php';

use App\models\BusModel;

class BusController
{
    public function findBus($params): array
    {
        $from = $params['from'] ?? null;
        $to = $params['to'] ?? null;

        if (!$from || !$to) {
            http_response_code(400);
            return ['error' => 'Missing required parameters'];
        }

        $model = new BusModel();
        return $model->getBusesForRoute($from, $to);
    }
}
