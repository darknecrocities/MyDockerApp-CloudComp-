<?php
header('Content-Type: application/json');

$host = 'mysql';
$dbname = 'haumonstersDB';
$username = 'dbmanager';
$password = '6cloudcom123!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query the exact table name specified in requirements
    $stmt = $pdo->query("SELECT id, name, password FROM playerstbl ORDER BY id ASC");
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($players, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database Connection Failed",
        "message" => $e->getMessage()
    ]);
}
?>
