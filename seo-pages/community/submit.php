<?php
// Community Submission Handler
// Saves theories, predictions, and art submissions

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid submission']);
    exit;
}

$type = $input['type'];
$dataDir = __DIR__ . '/data';

// Create data directory if it doesn't exist
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Determine which file to update
switch ($type) {
    case 'theory':
        $file = $dataDir . '/theories.json';
        $entry = [
            'id' => uniqid(),
            'name' => htmlspecialchars($input['name'] ?? 'Anonymous'),
            'category' => htmlspecialchars($input['category'] ?? 'other'),
            'title' => htmlspecialchars($input['title'] ?? ''),
            'theory' => htmlspecialchars($input['theory'] ?? ''),
            'evidence' => htmlspecialchars($input['evidence'] ?? ''),
            'timestamp' => $input['timestamp'] ?? date('c'),
            'approved' => true // Auto-approve to show immediately
        ];
        break;

    case 'prediction':
        $file = $dataDir . '/predictions.json';
        $entry = [
            'id' => uniqid(),
            'name' => htmlspecialchars($input['name'] ?? 'Anonymous'),
            'location' => htmlspecialchars($input['location'] ?? ''),
            'prediction' => htmlspecialchars($input['prediction'] ?? ''),
            'timestamp' => $input['timestamp'] ?? date('c'),
            'approved' => true // Auto-approve predictions
        ];
        break;

    case 'art':
        $file = $dataDir . '/art.json';
        $entry = [
            'id' => uniqid(),
            'artist' => htmlspecialchars($input['artist'] ?? 'Anonymous'),
            'title' => htmlspecialchars($input['title'] ?? 'Untitled'),
            'imageUrl' => filter_var($input['imageUrl'] ?? '', FILTER_SANITIZE_URL),
            'description' => htmlspecialchars($input['description'] ?? ''),
            'portfolio' => filter_var($input['portfolio'] ?? '', FILTER_SANITIZE_URL),
            'timestamp' => $input['timestamp'] ?? date('c'),
            'approved' => true // Auto-approve to show immediately
        ];
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown submission type']);
        exit;
}

// Load existing data
$data = [];
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true) ?? [];
}

// Add new entry at the beginning
array_unshift($data, $entry);

// Save
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

// Update stats
$statsFile = __DIR__ . '/stats.json';
$stats = file_exists($statsFile) ? json_decode(file_get_contents($statsFile), true) : [];
$stats['theories'] = count(file_exists($dataDir . '/theories.json') ? json_decode(file_get_contents($dataDir . '/theories.json'), true) : []);
$stats['predictions'] = count(file_exists($dataDir . '/predictions.json') ? json_decode(file_get_contents($dataDir . '/predictions.json'), true) : []);
$stats['art'] = count(file_exists($dataDir . '/art.json') ? json_decode(file_get_contents($dataDir . '/art.json'), true) : []);
file_put_contents($statsFile, json_encode($stats));

echo json_encode(['success' => true, 'id' => $entry['id']]);
