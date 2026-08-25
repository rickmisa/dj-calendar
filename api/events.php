<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function respond(mixed $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

function requestBody(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function validate(array $data): array
{
    $required = ['title', 'event_date', 'category'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            respond(['error' => "The {$field} field is required."], 422);
        }
    }

    $date = DateTime::createFromFormat('Y-m-d', (string) $data['event_date']);
    if (!$date || $date->format('Y-m-d') !== $data['event_date']) {
        respond(['error' => 'Please provide a valid event date.'], 422);
    }

    $categories = ['work', 'personal', 'meeting', 'birthday', 'holiday'];
    if (!in_array($data['category'], $categories, true)) {
        respond(['error' => 'Please choose a valid category.'], 422);
    }

    return [
        'title' => trim((string) $data['title']),
        'description' => trim((string) ($data['description'] ?? '')),
        'event_date' => $data['event_date'],
        'start_time' => $data['start_time'] ?: null,
        'end_time' => $data['end_time'] ?: null,
        'location' => trim((string) ($data['location'] ?? '')),
        'category' => $data['category'],
        'color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($data['color'] ?? '')) ? $data['color'] : '#e85d3f',
    ];
}

try {
    $pdo = db();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            respond(['error' => 'Month must use YYYY-MM format.'], 422);
        }
        $statement = $pdo->prepare('SELECT * FROM events WHERE event_date BETWEEN :start AND :end ORDER BY event_date, start_time, title');
        $statement->execute(['start' => $month . '-01', 'end' => date('Y-m-t', strtotime($month . '-01'))]);
        respond($statement->fetchAll());
    }

    if ($method === 'POST') {
        $event = validate(requestBody());
        $statement = $pdo->prepare('INSERT INTO events (title, description, event_date, start_time, end_time, location, category, color) VALUES (:title, :description, :event_date, :start_time, :end_time, :location, :category, :color)');
        $statement->execute($event);
        respond(['id' => (int) $pdo->lastInsertId(), 'message' => 'Event created.'], 201);
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        respond(['error' => 'A valid event id is required.'], 422);
    }

    if ($method === 'PUT') {
        $event = validate(requestBody());
        $event['id'] = $id;
        $statement = $pdo->prepare('UPDATE events SET title=:title, description=:description, event_date=:event_date, start_time=:start_time, end_time=:end_time, location=:location, category=:category, color=:color WHERE id=:id');
        $statement->execute($event);
        respond(['message' => 'Event updated.']);
    }

    if ($method === 'DELETE') {
        $statement = $pdo->prepare('DELETE FROM events WHERE id = :id');
        $statement->execute(['id' => $id]);
        respond(['message' => 'Event deleted.']);
    }

    respond(['error' => 'Method not allowed.'], 405);
} catch (PDOException $exception) {
    respond(['error' => 'Database unavailable. Import database.sql and check config.php.'], 503);
}
