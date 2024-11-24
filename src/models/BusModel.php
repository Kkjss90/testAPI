<?php
namespace App\models;

use App\utils\database;

class BusModel {
    public function getBusesForRoute($from, $to) {
        $pdo = Database::getConnection();

        $query = "
            SELECT b.direction, r.name AS route_name, s.arrival_time
            FROM buses b
            JOIN routes r ON b.route_id = r.id
            JOIN schedules s ON b.id = s.bus_id
            WHERE s.stop_id = :from
            AND b.id IN (
                SELECT DISTINCT b1.id
                FROM buses b1
                JOIN schedules s1 ON b1.id = s1.bus_id
                WHERE s1.stop_id = :to
            )
            ORDER BY s.arrival_time
            LIMIT 3;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['from' => $from, 'to' => $to]);

        $buses = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $buses[] = [
                'route' => "{$row['route_name']} ({$row['direction']})",
                'next_arrivals' => [$row['arrival_time']]
            ];
        }

        return [
            'from' => $from,
            'to' => $to,
            'buses' => $buses
        ];
    }
}
