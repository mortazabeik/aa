<?php
/**
 * Get unique values for filter dropdowns
 * GET /api/unique-values.php?field=birth_place
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$db = getDB();
$field = $_GET['field'] ?? null;

// Map frontend field names to database columns
$fieldMap = [
    'veteranStatus' => 'veteran_status',
    'birthPlace' => 'birth_place',
    'burialPlace' => 'burial_place',
    'fileLocation' => 'file_location',
    'gender' => 'gender',
    'nationality' => 'nationality',
    'religion' => 'religion',
    'occupation' => 'occupation',
    'servingUnit' => 'serving_unit',
    // Also allow snake_case
    'veteran_status' => 'veteran_status',
    'birth_place' => 'birth_place',
    'burial_place' => 'burial_place',
    'file_location' => 'file_location',
    'serving_unit' => 'serving_unit',
];

if (!$field) {
    // Return all unique values for all fields
    $result = [];
    foreach (array_unique(array_values($fieldMap)) as $column) {
        $stmt = $db->prepare("SELECT DISTINCT $column FROM martyrs WHERE $column IS NOT NULL AND $column != '' ORDER BY $column");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $result[$column] = $values;
    }
    jsonResponse($result);
}

if (!isset($fieldMap[$field])) {
    jsonResponse(['error' => 'Invalid field name'], 400);
}

$column = $fieldMap[$field];
$stmt = $db->prepare("SELECT DISTINCT $column FROM martyrs WHERE $column IS NOT NULL AND $column != '' ORDER BY $column");
$stmt->execute();
$values = $stmt->fetchAll(PDO::FETCH_COLUMN);

jsonResponse($values);
