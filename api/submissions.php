<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

switch ($method) {
    case 'GET':
        handleGet($db);
        break;
    case 'POST':
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
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE id = ?");
        $stmt->execute([$id]);
        $submission = $stmt->fetch();
        
        if ($submission) {
            jsonResponse(formatSubmission($submission));
        } else {
            jsonResponse(['error' => 'Submission not found'], 404);
        }
        return;
    }
    
    // Get all submissions with optional filters
    $where = [];
    $params = [];
    
    if (!empty($_GET['status'])) {
        $where[] = "status = ?";
        $params[] = $_GET['status'];
    }
    
    if (!empty($_GET['martyrId'])) {
        $where[] = "martyr_id = ?";
        $params[] = intval($_GET['martyrId']);
    }
    
    $sql = "SELECT s.*, m.first_name, m.last_name 
            FROM submissions s 
            LEFT JOIN martyrs m ON s.martyr_id = m.id";
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " ORDER BY s.submitted_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll();
    
    jsonResponse(array_map('formatSubmission', $submissions));
}

function handlePost($db) {
    $data = getJsonInput();
    
    $sql = "INSERT INTO submissions (type, martyr_id, file_url, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $data['type'] ?? '',
        intval($data['martyrId']),
        $data['fileUrl'] ?? ''
    ]);
    
    $id = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT * FROM submissions WHERE id = ?");
    $stmt->execute([$id]);
    $submission = $stmt->fetch();
    
    jsonResponse(formatSubmission($submission), 201);
}

function handlePut($db) {
    $data = getJsonInput();
    $id = $data['id'] ?? $_GET['id'] ?? null;
    $action = $data['action'] ?? '';
    
    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }
    
    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE submissions SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE submissions SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        jsonResponse(['error' => 'Invalid action'], 400);
    }
    
    $stmt = $db->prepare("SELECT * FROM submissions WHERE id = ?");
    $stmt->execute([$id]);
    $submission = $stmt->fetch();
    
    if ($submission) {
        jsonResponse(formatSubmission($submission));
    } else {
        jsonResponse(['error' => 'Submission not found'], 404);
    }
}

function handleDelete($db) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }
    
    $stmt = $db->prepare("DELETE FROM submissions WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse(['success' => true, 'message' => 'Submission deleted']);
    } else {
        jsonResponse(['error' => 'Submission not found'], 404);
    }
}

function formatSubmission($row) {
    return [
        'id' => strval($row['id']),
        'type' => $row['type'] ?? '',
        'martyrId' => strval($row['martyr_id']),
        'martyrName' => isset($row['first_name']) ? $row['first_name'] . ' ' . $row['last_name'] : '',
        'fileUrl' => $row['file_url'] ?? '',
        'status' => $row['status'] ?? 'pending',
        'submittedAt' => $row['submitted_at'] ?? ''
    ];
}
