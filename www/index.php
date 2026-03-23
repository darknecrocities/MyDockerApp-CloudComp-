<?php
header('Content-Type: text/html; charset=UTF-8');

$host = 'mysql';
$dbname = 'haumonstersDB';
$username = 'dbmanager';
$password = '6cloudcom123!';

$message = '';
$messageType = '';

// Handle JSON API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');
    $pass = trim($input['password'] ?? '');

    if (empty($name) || empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Name and password are required.']);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO playerstbl (name, password) VALUES (:name, :password)");
        $stmt->execute([':name' => $name, ':password' => $pass]);
        echo json_encode(['success' => true, 'message' => 'Player added successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Handle JSON GET for fetching players
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api']) && $_GET['api'] === 'players') {
    header('Content-Type: application/json');
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query("SELECT id, name FROM playerstbl ORDER BY id DESC");
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'players' => $players]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Handle traditional form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (empty($name) || empty($pass)) {
        $message = 'Name and password are required.';
        $messageType = 'error';
    } else {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("INSERT INTO playerstbl (name, password) VALUES (:name, :password)");
            $stmt->execute([':name' => $name, ':password' => $pass]);
            $message = 'Player added successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all players
$players = [];
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT id, name FROM playerstbl ORDER BY id DESC");
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Could not connect to database: ' . $e->getMessage();
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HauMonsters — Player Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">🎮</div>
            <h1>HauMonsters</h1>
            <p class="subtitle">Player Management System</p>
        </header>

        <!-- Status Message -->
        <div id="status-message" class="<?php echo $messageType ? "message $messageType show" : 'message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

        <!-- Add Player Form -->
        <section class="card">
            <h2>➕ Add New Player</h2>
            <form id="player-form" method="POST" action="">
                <div class="form-group">
                    <label for="name">Player Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter player name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="off">
                </div>
                <button type="submit" id="submit-btn">
                    <span class="btn-text">Add Player</span>
                    <span class="btn-loader" style="display:none;">Adding...</span>
                </button>
            </form>
        </section>

        <!-- Players Table -->
        <section class="card">
            <h2>👥 Registered Players <span class="badge" id="player-count"><?php echo count($players); ?></span></h2>
            <div class="table-wrapper">
                <table id="players-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Player Name</th>
                        </tr>
                    </thead>
                    <tbody id="players-tbody">
                        <?php if (empty($players)): ?>
                            <tr class="empty-row">
                                <td colspan="2">No players registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($players as $player): ?>
                                <tr>
                                    <td><span class="id-badge">#<?php echo htmlspecialchars($player['id']); ?></span></td>
                                    <td><?php echo htmlspecialchars($player['name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- New Testing Links Section -->
        <section class="card testing-links">
            <h2>🛠️ Verification Tools</h2>
            <div class="btn-group">
                <a href="test.php" target="_blank" class="btn btn-secondary">Check PHP Info</a>
                <a href="fetch.php" target="_blank" class="btn btn-secondary">Check JSON API</a>
            </div>
        </section>

        <footer>
            <p>HauMonsters &copy; <?php echo date('Y'); ?> — Powered by NGINX · PHP · MySQL</p>
        </footer>
    </div>

    <script src="app.js"></script>
</body>
</html>
