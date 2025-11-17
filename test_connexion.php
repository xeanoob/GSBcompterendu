<?php
require_once 'modele/bd.inc.php';

echo "<h2>Test de connexion MySQL</h2>";

try {
    $pdo = connexionPDO();
    echo "<p style='color: green;'>✓ Connexion réussie à la base de données !</p>";
    
    // Test de requête
    $stmt = $pdo->query("SELECT COUNT(*) as nb FROM collaborateur");
    $result = $stmt->fetch();
    echo "<p>Nombre de collaborateurs : " . $result['nb'] . "</p>";
    
    echo "<p style='color: green;'>✓ Base de données opérationnelle !</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur de connexion : " . $e->getMessage() . "</p>";
}
?>
