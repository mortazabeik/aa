<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

switch ($method) {
    case 'GET':
        handleGet($db);
        break;
    case 'POST':
        requireAuth();
        handlePost($db);
        break;
    case 'PUT':
        requireAuth();
        handlePut($db);
        break;
    case 'DELETE':
        requireAuth();
        handleDelete($db);
        break;
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}

function handleGet($db) {
    $stmt = $db->query("SELECT * FROM sliders ORDER BY sort_order ASC");
    $sliders = $stmt->fetchAll();
    
    jsonResponse(array_map('formatSlider', $sliders));
}

function handlePost($db) {
    $data = getJsonInput();
    
    // Get max order
    $stmt = $db->query("SELECT MAX(sort_order) as max_order FROM sliders");
    $result = $stmt->fetch();
    $nextOrder = ($result['max_order'] ?? -1) + 1;
    
    $sql = "INSERT INTO sliders (url, sort_order) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $data['url'] ?? '',
        $nextOrder
    ]);
    
    $id = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch();
    
    jsonResponse(formatSlider($slider), 201);
}

function handlePut($db) {
    $data = getJsonInput();
    
    // Reorder sliders
    if (isset($data['sliders']) && is_array($data['sliders'])) {
        foreach ($data['sliders'] as $index => $slider) {
            $stmt = $db->prepare("UPDATE sliders SET sort_order = ? WHERE id = ?");
            $stmt->execute([$index, $slider['id']]);
        }
        
        $stmt = $db->query("SELECT * FROM sliders ORDER BY sort_order ASC");
        $sliders = $stmt->fetchAll();
        
        jsonResponse(array_map('formatSlider', $sliders));
        return;
    }
    
    jsonResponse(['error' => 'Invalid data'], 400);
}

function handleDelete($db) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }
    
    $stmt = $db->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse(['success' => true, 'message' => 'Slider deleted']);
    } else {
        jsonResponse(['error' => 'Slider not found'], 404);
    }
}

function formatSlider($row) {
    return [
        'id' => strval($row['id']),
        'url' => $row['url'] ?? '',
        'order' => intval($row['sort_order'] ?? 0)
    ];
}
