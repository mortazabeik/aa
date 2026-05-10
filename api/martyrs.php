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
    // Check if specific ID is requested
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM martyrs WHERE id = ?");
        $stmt->execute([$id]);
        $martyr = $stmt->fetch();
        
        if ($martyr) {
            jsonResponse(formatMartyr($martyr));
        } else {
            jsonResponse(['error' => 'Martyr not found'], 404);
        }
        return;
    }
    
    // Build search query
    $where = [];
    $params = [];
    
    // Name search
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $where[] = "(first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
        $params = array_merge($params, [$search, $search, $search]);
    }
    
    // National ID
    if (!empty($_GET['nationalId'])) {
        $where[] = "national_id LIKE ?";
        $params[] = '%' . $_GET['nationalId'] . '%';
    }
    
    // Code Isar
    if (!empty($_GET['codeIsar'])) {
        $where[] = "code_isar LIKE ?";
        $params[] = '%' . $_GET['codeIsar'] . '%';
    }
    
    // Veteran Status
    if (!empty($_GET['veteranStatus'])) {
        $where[] = "veteran_status LIKE ?";
        $params[] = '%' . $_GET['veteranStatus'] . '%';
    }
    
    // Age
    if (!empty($_GET['age'])) {
        $where[] = "age = ?";
        $params[] = intval($_GET['age']);
    }
    
    // Birth date filters
    if (!empty($_GET['birthYear'])) {
        $where[] = "birth_year = ?";
        $params[] = $_GET['birthYear'];
    }
    if (!empty($_GET['birthMonth'])) {
        $where[] = "CAST(birth_month AS UNSIGNED) = ?";
        $params[] = intval($_GET['birthMonth']);
    }
    if (!empty($_GET['birthDay'])) {
        $where[] = "CAST(birth_day AS UNSIGNED) = ?";
        $params[] = intval($_GET['birthDay']);
    }
    
    // Martyrdom date filters
    if (!empty($_GET['martyrdomYear'])) {
        $where[] = "martyrdom_year = ?";
        $params[] = $_GET['martyrdomYear'];
    }
    if (!empty($_GET['martyrdomMonth'])) {
        $where[] = "CAST(martyrdom_month AS UNSIGNED) = ?";
        $params[] = intval($_GET['martyrdomMonth']);
    }
    if (!empty($_GET['martyrdomDay'])) {
        $where[] = "CAST(martyrdom_day AS UNSIGNED) = ?";
        $params[] = intval($_GET['martyrdomDay']);
    }
    
    // Place filters
    if (!empty($_GET['birthPlace'])) {
        $where[] = "birth_place LIKE ?";
        $params[] = '%' . $_GET['birthPlace'] . '%';
    }
    if (!empty($_GET['burialPlace'])) {
        $where[] = "burial_place LIKE ?";
        $params[] = '%' . $_GET['burialPlace'] . '%';
    }
    if (!empty($_GET['fileLocation'])) {
        $where[] = "file_location LIKE ?";
        $params[] = '%' . $_GET['fileLocation'] . '%';
    }
    
    // Other filters
    if (!empty($_GET['gender'])) {
        $where[] = "gender = ?";
        $params[] = $_GET['gender'];
    }
    if (!empty($_GET['nationality'])) {
        $where[] = "nationality = ?";
        $params[] = $_GET['nationality'];
    }
    if (!empty($_GET['religion'])) {
        $where[] = "religion = ?";
        $params[] = $_GET['religion'];
    }
    if (!empty($_GET['occupation'])) {
        $where[] = "occupation LIKE ?";
        $params[] = '%' . $_GET['occupation'] . '%';
    }
    if (!empty($_GET['servingUnit'])) {
        $where[] = "serving_unit LIKE ?";
        $params[] = '%' . $_GET['servingUnit'] . '%';
    }
    
    // Build query
    $sql = "SELECT * FROM martyrs";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY id ASC";
    
    // Pagination
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(100, max(1, intval($_GET['limit'] ?? 24)));
    $offset = ($page - 1) * $limit;
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM martyrs";
    if (!empty($where)) {
        $countSql .= " WHERE " . implode(" AND ", $where);
    }
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Add pagination to main query
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $martyrs = $stmt->fetchAll();
    
    jsonResponse([
        'data' => array_map('formatMartyr', $martyrs),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => intval($total),
            'totalPages' => ceil($total / $limit)
        ]
    ]);
}

function handlePost($db) {
    $data = getJsonInput();
    
    $sql = "INSERT INTO martyrs (
        code_isar, national_id, veteran_status, first_name, last_name, father_name,
        gender, nationality, religion, birth_date, birth_year, birth_month, birth_day,
        martyrdom_date, martyrdom_year, martyrdom_month, martyrdom_day, age,
        birth_place, file_location, burial_place, education, occupation, marital_status,
        serving_unit, membership_type, event_stream, operation_zone, enemy, military_operation, profile_image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $data['codeIsar'] ?? '',
        $data['nationalId'] ?? '',
        $data['veteranStatus'] ?? '',
        $data['firstName'] ?? '',
        $data['lastName'] ?? '',
        $data['fatherName'] ?? '',
        $data['gender'] ?? '',
        $data['nationality'] ?? '',
        $data['religion'] ?? '',
        $data['birthDate'] ?? '',
        $data['birthYear'] ?? '',
        $data['birthMonth'] ?? '',
        $data['birthDay'] ?? '',
        $data['martyrdomDate'] ?? '',
        $data['martyrdomYear'] ?? '',
        $data['martyrdomMonth'] ?? '',
        $data['martyrdomDay'] ?? '',
        $data['age'] ?? null,
        $data['birthPlace'] ?? '',
        $data['fileLocation'] ?? '',
        $data['burialPlace'] ?? '',
        $data['education'] ?? '',
        $data['occupation'] ?? '',
        $data['maritalStatus'] ?? '',
        $data['servingUnit'] ?? '',
        $data['membershipType'] ?? '',
        $data['eventStream'] ?? '',
        $data['operationZone'] ?? '',
        $data['enemy'] ?? '',
        $data['militaryOperation'] ?? '',
        $data['profileImage'] ?? ''
    ]);
    
    $id = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT * FROM martyrs WHERE id = ?");
    $stmt->execute([$id]);
    $martyr = $stmt->fetch();
    
    jsonResponse(formatMartyr($martyr), 201);
}

function handlePut($db) {
    $data = getJsonInput();
    $id = $data['id'] ?? $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }
    
    $sql = "UPDATE martyrs SET 
        code_isar = ?, national_id = ?, veteran_status = ?, first_name = ?, last_name = ?, father_name = ?,
        gender = ?, nationality = ?, religion = ?, birth_date = ?, birth_year = ?, birth_month = ?, birth_day = ?,
        martyrdom_date = ?, martyrdom_year = ?, martyrdom_month = ?, martyrdom_day = ?, age = ?,
        birth_place = ?, file_location = ?, burial_place = ?, education = ?, occupation = ?, marital_status = ?,
        serving_unit = ?, membership_type = ?, event_stream = ?, operation_zone = ?, enemy = ?, military_operation = ?, profile_image = ?
        WHERE id = ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $data['codeIsar'] ?? '',
        $data['nationalId'] ?? '',
        $data['veteranStatus'] ?? '',
        $data['firstName'] ?? '',
        $data['lastName'] ?? '',
        $data['fatherName'] ?? '',
        $data['gender'] ?? '',
        $data['nationality'] ?? '',
        $data['religion'] ?? '',
        $data['birthDate'] ?? '',
        $data['birthYear'] ?? '',
        $data['birthMonth'] ?? '',
        $data['birthDay'] ?? '',
        $data['martyrdomDate'] ?? '',
        $data['martyrdomYear'] ?? '',
        $data['martyrdomMonth'] ?? '',
        $data['martyrdomDay'] ?? '',
        $data['age'] ?? null,
        $data['birthPlace'] ?? '',
        $data['fileLocation'] ?? '',
        $data['burialPlace'] ?? '',
        $data['education'] ?? '',
        $data['occupation'] ?? '',
        $data['maritalStatus'] ?? '',
        $data['servingUnit'] ?? '',
        $data['membershipType'] ?? '',
        $data['eventStream'] ?? '',
        $data['operationZone'] ?? '',
        $data['enemy'] ?? '',
        $data['militaryOperation'] ?? '',
        $data['profileImage'] ?? '',
        $id
    ]);
    
    $stmt = $db->prepare("SELECT * FROM martyrs WHERE id = ?");
    $stmt->execute([$id]);
    $martyr = $stmt->fetch();
    
    if ($martyr) {
        jsonResponse(formatMartyr($martyr));
    } else {
        jsonResponse(['error' => 'Martyr not found'], 404);
    }
}

function handleDelete($db) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }
    
    $stmt = $db->prepare("DELETE FROM martyrs WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse(['success' => true, 'message' => 'Martyr deleted']);
    } else {
        jsonResponse(['error' => 'Martyr not found'], 404);
    }
}

function formatMartyr($row) {
    return [
        'id' => strval($row['id']),
        'codeIsar' => $row['code_isar'] ?? '',
        'nationalId' => $row['national_id'] ?? '',
        'veteranStatus' => $row['veteran_status'] ?? '',
        'firstName' => $row['first_name'] ?? '',
        'lastName' => $row['last_name'] ?? '',
        'fatherName' => $row['father_name'] ?? '',
        'gender' => $row['gender'] ?? '',
        'nationality' => $row['nationality'] ?? '',
        'religion' => $row['religion'] ?? '',
        'birthDate' => $row['birth_date'] ?? '',
        'birthYear' => $row['birth_year'] ?? '',
        'birthMonth' => $row['birth_month'] ?? '',
        'birthDay' => $row['birth_day'] ?? '',
        'martyrdomDate' => $row['martyrdom_date'] ?? '',
        'martyrdomYear' => $row['martyrdom_year'] ?? '',
        'martyrdomMonth' => $row['martyrdom_month'] ?? '',
        'martyrdomDay' => $row['martyrdom_day'] ?? '',
        'age' => $row['age'] !== null ? intval($row['age']) : null,
        'birthPlace' => $row['birth_place'] ?? '',
        'fileLocation' => $row['file_location'] ?? '',
        'burialPlace' => $row['burial_place'] ?? '',
        'education' => $row['education'] ?? '',
        'occupation' => $row['occupation'] ?? '',
        'maritalStatus' => $row['marital_status'] ?? '',
        'servingUnit' => $row['serving_unit'] ?? '',
        'membershipType' => $row['membership_type'] ?? '',
        'eventStream' => $row['event_stream'] ?? '',
        'operationZone' => $row['operation_zone'] ?? '',
        'enemy' => $row['enemy'] ?? '',
        'militaryOperation' => $row['military_operation'] ?? '',
        'profileImage' => $row['profile_image'] ?? ''
    ];
}
