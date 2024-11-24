<?php

use App\controllers\BusController;
use App\utils\Database;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/utils/Database.php';
require_once __DIR__ . '/../../src/controllers/BusController.php';


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
if ($uri === '/setup') {
    try {
        $pdo = Database::getConnection();
        echo "Начинаем настройку базы данных...<br>";

        $queries = array(
            "CREATE TABLE IF NOT EXISTS stops (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS routes (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS buses (
                id SERIAL PRIMARY KEY,
                route_id INT REFERENCES routes(id),
                direction VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS schedules (
                id SERIAL PRIMARY KEY,
                bus_id INT REFERENCES buses(id),
                stop_id INT REFERENCES stops(id),
                arrival_time TIME NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS route_stops (
                route_id INT REFERENCES routes(id),
                stop_id INT REFERENCES stops(id),
                stop_order INT NOT NULL,
                PRIMARY KEY (route_id, stop_id)
            )"
        );

        foreach ($queries as $query) {
            $pdo->exec($query);
        }

        echo "Таблицы успешно созданы.<br>";

        $data = [
            "INSERT INTO stops (name) VALUES ('Остановка А'), ('Остановка Б'), ('Остановка В')",
            "INSERT INTO routes (name) VALUES ('Маршрут №1')",
            "INSERT INTO buses (route_id, direction) VALUES (1, 'в сторону Остановки В')",
            "INSERT INTO route_stops (route_id, stop_id, stop_order) VALUES
                (1, 1, 1),
                (1, 2, 2),
                (1, 3, 3)",
            "INSERT INTO schedules (bus_id, stop_id, arrival_time) VALUES
                (1, 1, '08:00'),
                (1, 1, '08:30'),
                (1, 1, '09:00'),
                (1, 2, '08:10'),
                (1, 2, '08:40'),
                (1, 2, '09:10'),
                (1, 3, '08:20'),
                (1, 3, '08:50'),
                (1, 3, '09:20')"
        ];

        foreach ($data as $query) {
            $pdo->exec($query);
        }

        echo "Данные успешно добавлены.<br>";
    } catch (Exception $e) {
        echo "Ошибка при настройке базы данных: " . $e->getMessage();
    }
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($uri === '/api/find-bus' && $method === 'GET') {
    $controller = new BusController();
    echo json_encode($controller->findBus($_GET),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
