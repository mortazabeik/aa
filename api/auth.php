<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$data = getJsonInput();
$action = $data['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($data);
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        handleCheck();
        break;
    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

function handleLogin($data) {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        jsonResponse(['error' => 'Username and password are required'], 400);
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_username'] = $username;
        jsonResponse([
            'success' => true,
            'message' => 'Login successful',
            'user' => ['username' => $username]
        ]);
    } else {
        jsonResponse(['error' => 'Invalid username or password'], 401);
    }
}

function handleLogout() {
    $_SESSION = [];
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logged out']);
}

function handleCheck() {
    if (isAuthenticated()) {
        jsonResponse([
            'authenticated' => true,
            'user' => ['username' => $_SESSION['admin_username'] ?? 'admin']
        ]);
    } else {
        jsonResponse(['authenticated' => false]);
    }
}
