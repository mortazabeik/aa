<?php
require_once __DIR__ . '/../includes/functions.php';
ensure_session();
session_destroy();
header('Location: ' . url('admin/login.php'));
exit;
