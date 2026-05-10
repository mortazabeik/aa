<?php
/**
 * Import Data Script
 * Run this script once to import data from db.txt into MySQL
 * 
 * Usage: php import-data.php path/to/db.txt
 */

require_once 'config.php';

// Disable output buffering for real-time progress
if (php_sapi_name() === 'cli') {
    ob_implicit_flush(true);
}

function parseDbFile($filePath) {
    if (!file_exists($filePath)) {
        die("Error: File not found: $filePath\n");
    }
    
    $content = file_get_contents($filePath);
    $lines = array_filter(explode("\n", $content), function($line) {
        return trim($line) !== '';
    });
    
    // Skip header line
    $dataLines = array_slice($lines, 1);
    $martyrs = [];
    
    foreach ($dataLines as $index => $line) {
        // Remove leading index like "1: " or "2: "
        $dataStr = preg_replace('/^\d+:\s*/', '', $line);
        
        $parts = [];
        $inQuotes = false;
        $currentPart = "";
        
        for ($i = 0; $i < strlen($dataStr); $i++) {
            $char = $dataStr[$i];
            if ($char === '"') {
                $inQuotes = !$inQuotes;
            } elseif ($char === ',' && !$inQuotes) {
                $parts[] = $currentPart;
                $currentPart = "";
            } else {
                $currentPart .= $char;
            }
        }
        $parts[] = $currentPart;
        
        // Pad array if shorter
        while (count($parts) < 24) {
            $parts[] = "";
        }
        
        // Map parts to fields
        list(
            $codeIsar, $nationalId, $veteranStatus, $firstName, $lastName, $fatherName,
            $gender, $nationality, $religion, $birthDateStr, $martyrdomDateStr, $age,
            $birthPlace, $fileLocation, $burialPlace, $education, $occupation, $maritalStatus,
            $servingUnit, $membershipType, $eventStream, $operationZone, $enemy, $militaryOperation
        ) = array_map('trim', $parts);
        
        // Parse birth date
        $bYear = $bMonth = $bDay = "";
        if ($birthDateStr && strpos($birthDateStr, '/') !== false) {
            $dParts = explode('/', $birthDateStr);
            if (count($dParts) === 3) {
                list($bYear, $bMonth, $bDay) = $dParts;
            }
        }
        
        // Parse martyrdom date
        $mYear = $mMonth = $mDay = "";
        if ($martyrdomDateStr && strpos($martyrdomDateStr, '/') !== false) {
            $dParts = explode('/', $martyrdomDateStr);
            if (count($dParts) === 3) {
                list($mYear, $mMonth, $mDay) = $dParts;
            }
        }
        
        $martyrs[] = [
            'code_isar' => $codeIsar,
            'national_id' => $nationalId,
            'veteran_status' => $veteranStatus,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'father_name' => $fatherName,
            'gender' => $gender,
            'nationality' => $nationality,
            'religion' => $religion,
            'birth_date' => $birthDateStr,
            'birth_year' => $bYear,
            'birth_month' => $bMonth,
            'birth_day' => $bDay,
            'martyrdom_date' => $martyrdomDateStr,
            'martyrdom_year' => $mYear,
            'martyrdom_month' => $mMonth,
            'martyrdom_day' => $mDay,
            'age' => $age !== '' ? floatval($age) : null,
            'birth_place' => $birthPlace,
            'file_location' => $fileLocation,
            'burial_place' => $burialPlace,
            'education' => $education,
            'occupation' => $occupation,
            'marital_status' => $maritalStatus,
            'serving_unit' => $servingUnit,
            'membership_type' => $membershipType,
            'event_stream' => $eventStream,
            'operation_zone' => $operationZone,
            'enemy' => $enemy,
            'military_operation' => $militaryOperation,
            'profile_image' => ''
        ];
    }
    
    return $martyrs;
}

function importToDatabase($martyrs) {
    $db = getDB();
    
    // Prepare insert statement
    $sql = "INSERT INTO martyrs (
        code_isar, national_id, veteran_status, first_name, last_name, father_name,
        gender, nationality, religion, birth_date, birth_year, birth_month, birth_day,
        martyrdom_date, martyrdom_year, martyrdom_month, martyrdom_day, age,
        birth_place, file_location, burial_place, education, occupation, marital_status,
        serving_unit, membership_type, event_stream, operation_zone, enemy, military_operation, profile_image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    
    $successCount = 0;
    $errorCount = 0;
    $total = count($martyrs);
    
    echo "Starting import of $total records...\n\n";
    
    foreach ($martyrs as $index => $martyr) {
        try {
            $stmt->execute([
                $martyr['code_isar'],
                $martyr['national_id'],
                $martyr['veteran_status'],
                $martyr['first_name'],
                $martyr['last_name'],
                $martyr['father_name'],
                $martyr['gender'],
                $martyr['nationality'],
                $martyr['religion'],
                $martyr['birth_date'],
                $martyr['birth_year'],
                $martyr['birth_month'],
                $martyr['birth_day'],
                $martyr['martyrdom_date'],
                $martyr['martyrdom_year'],
                $martyr['martyrdom_month'],
                $martyr['martyrdom_day'],
                $martyr['age'],
                $martyr['birth_place'],
                $martyr['file_location'],
                $martyr['burial_place'],
                $martyr['education'],
                $martyr['occupation'],
                $martyr['marital_status'],
                $martyr['serving_unit'],
                $martyr['membership_type'],
                $martyr['event_stream'],
                $martyr['operation_zone'],
                $martyr['enemy'],
                $martyr['military_operation'],
                $martyr['profile_image']
            ]);
            $successCount++;
            
            // Show progress every 100 records
            if (($index + 1) % 100 === 0) {
                echo "Progress: " . ($index + 1) . "/$total records imported\n";
            }
        } catch (PDOException $e) {
            $errorCount++;
            echo "Error importing record " . ($index + 1) . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Import Complete ===\n";
    echo "Successfully imported: $successCount records\n";
    echo "Errors: $errorCount records\n";
}

// Main execution
if (php_sapi_name() === 'cli') {
    // Command line usage
    if ($argc < 2) {
        echo "Usage: php import-data.php <path-to-db.txt>\n";
        echo "Example: php import-data.php ../src/app/imports/db.txt\n";
        exit(1);
    }
    
    $filePath = $argv[1];
    
    echo "Parsing file: $filePath\n";
    $martyrs = parseDbFile($filePath);
    echo "Found " . count($martyrs) . " records to import\n\n";
    
    importToDatabase($martyrs);
} else {
    // Web usage - require authentication
    requireAuth();
    
    // Check if file path is provided
    $filePath = $_POST['file_path'] ?? $_GET['file_path'] ?? null;
    
    if (!$filePath) {
        jsonResponse(['error' => 'file_path is required'], 400);
    }
    
    try {
        $martyrs = parseDbFile($filePath);
        importToDatabase($martyrs);
        jsonResponse([
            'success' => true,
            'message' => 'Import completed',
            'count' => count($martyrs)
        ]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
