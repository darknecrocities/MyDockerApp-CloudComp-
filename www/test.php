<?php
/**
 * System Diagnostic & Test Page
 * Purpose: Verify environment health for the Cloud Computing Exam.
 */

$host = 'mysql';
$dbname = 'haumonstersDB';
$username = 'dbmanager';
$password = '6cloudcom123!';

$results = [
    'PHP Version' => [
        'value' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, '8.0.0', '>=') ? 'PASS' : 'WARNING'
    ],
    'Server Software' => [
        'value' => $_SERVER['SERVER_SOFTWARE'],
        'status' => strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false ? 'PASS' : 'INFO'
    ],
    'MySQL Extension' => [
        'value' => extension_loaded('pdo_mysql') ? 'Installed' : 'Missing',
        'status' => extension_loaded('pdo_mysql') ? 'PASS' : 'FAIL'
    ],
];

// Test Database Connection
try {
    $start = microtime(true);
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2
    ]);
    $end = microtime(true);
    $latency = round(($end - $start) * 1000, 2);
    
    $results['Database Connectivity'] = [
        'value' => "Connected to $host ($latency ms)",
        'status' => 'PASS'
    ];
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM playerstbl");
    $count = $stmt->fetchColumn();
    $results['Database Table (playerstbl)'] = [
        'value' => "Table exists ($count records found)",
        'status' => 'PASS'
    ];
} catch (Exception $e) {
    $results['Database Connectivity'] = [
        'value' => "Failed: " . $e->getMessage(),
        'status' => 'FAIL'
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Diagnostics — HauMonsters</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #030308;
            --card: rgba(22, 22, 38, 0.7);
            --text: #ffffff;
            --accent: #ff416c;
            --pass: #00f2fe;
            --fail: #ff4d4d;
            --info: #3498db;
        }
        body {
            font-family: 'Outfit', -apple-system, sans-serif;
            background: var(--bg);
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 65, 108, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(30, 60, 200, 0.1) 0%, transparent 40%);
            color: var(--text);
            display: flex;
            justify-content: center;
            padding: 40px 20px;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 700px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: var(--accent);
            margin: 0;
            font-size: 1.8rem;
        }
        .card {
            background: var(--card);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        th {
            color: #888;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status.PASS { background: rgba(46, 204, 113, 0.2); color: var(--pass); }
        .status.FAIL { background: rgba(231, 76, 60, 0.2); color: var(--fail); }
        .status.INFO { background: rgba(52, 152, 219, 0.2); color: var(--info); }
        .status.WARNING { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        
        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 0.85rem;
            color: #666;
        }
        .footer a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛠️ System Diagnostics</h1>
            <p>Environment Validation Report</p>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Test Component</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $label => $data): ?>
                    <tr>
                        <td><strong><?php echo $label; ?></strong></td>
                        <td><span class="status <?php echo $data['status']; ?>"><?php echo $data['status']; ?></span></td>
                        <td><?php echo htmlspecialchars($data['value']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>Generated at <?php echo date('Y-m-d H:i:s T'); ?> | <a href="/index.php">Return to App</a></p>
        </div>
    </div>
</body>
</html>
