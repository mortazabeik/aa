<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$db = getDB();

// Get unique values for filter dropdowns
$filters = [
    'veteranStatus' => getUniqueValues($db, 'veteran_status'),
    'birthPlaces' => getUniqueValues($db, 'birth_place'),
    'burialPlaces' => getUniqueValues($db, 'burial_place'),
    'fileLocations' => getUniqueValues($db, 'file_location'),
    'genders' => getUniqueValues($db, 'gender'),
    'nationalities' => getUniqueValues($db, 'nationality'),
    'religions' => getUniqueValues($db, 'religion'),
    'occupations' => getUniqueValues($db, 'occupation'),
    'servingUnits' => getUniqueValues($db, 'serving_unit'),
];

jsonResponse($filters);

function getUniqueValues($db, $column) {
    $stmt = $db->prepare("SELECT DISTINCT $column FROM martyrs WHERE $column IS NOT NULL AND $column != '' ORDER BY $column");
    $stmt->execute();
    return array_column($stmt->fetchAll(), $column);
}
