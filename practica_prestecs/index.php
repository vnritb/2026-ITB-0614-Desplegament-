<?php
// 1. Configuració de la connexió llegint variables d'entorn
// Si la variable DB_HOST no existeix, fem servir 'c_bd3' per defecte
$host = getenv('DB_HOST') ?: 'c_bd3';
$user = getenv('MYSQL_USER') ?: 'user_dawe';
$pass = getenv('MYSQL_PASSWORD') ?: 'pwd';
$db   = getenv('MYSQL_DATABASE') ?: 'db_dawe';

// 2. Intent de connexió
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("<div style='color:red'><h1>❌ Error de Connexió</h1><p>" . $conn->connect_error . "</p></div>");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Préstecs - DAW</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin-bottom: 20px; border-left: 5px solid #2ecc71; background: #e8f8f5; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2c3e50; color: white; }
        .logo { float: right; }
    </style>
</head>
<body>

<div class="container">
    <img src="assets/logo.png" alt="Logo Institut" class="logo" width="120">
    
    <h1>💻 Inventari de Préstecs</h1>
    
    <div class="status">
        <strong>📍 Connectat a:</strong> <?php echo htmlspecialchars($host); ?> <br>
        <strong>📂 Base de dades:</strong> <?php echo htmlspecialchars($db); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Alumne</th>
                <th>ID Equip</th>
                <th>Estat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT id, alumne, equip_id, estat FROM prestecs";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['alumne']}</td>
                            <td>{$row['equip_id']}</td>
                            <td>{$row['estat']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No s'han trobat registres.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php $conn->close(); ?>