<?php
/**
 * Calculator page
 * A simple calculator application
 */

require_once 'includes/config.php';

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num1 = $_POST['num1'] ?? '';
    $num2 = $_POST['num2'] ?? '';
    $operation = $_POST['operation'] ?? '';
    
    if (is_numeric($num1) && is_numeric($num2)) {
        switch ($operation) {
            case 'add':
                $result = $num1 + $num2;
                break;
            case 'subtract':
                $result = $num1 - $num2;
                break;
            case 'multiply':
                $result = $num1 * $num2;
                break;
            case 'divide':
                if ($num2 != 0) {
                    $result = $num1 / $num2;
                } else {
                    $error = 'Division durch Null ist nicht möglich!';
                }
                break;
            default:
                $error = 'Ungültige Operation!';
        }
    } else {
        $error = 'Bitte geben Sie gültige Zahlen ein!';
    }
}

$pageTitle = 'Taschenrechner';
ob_start();
?>

<div class="calculator">
    <h1>Taschenrechner</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($result !== null): ?>
        <div class="alert alert-success">
            Ergebnis: <?php echo htmlspecialchars($result); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" class="calculator-form">
        <div class="form-group">
            <label for="num1">Erste Zahl:</label>
            <input type="number" id="num1" name="num1" step="any" required>
        </div>

        <div class="form-group">
            <label for="operation">Operation:</label>
            <select id="operation" name="operation" required>
                <option value="add">Addition (+)</option>
                <option value="subtract">Subtraktion (-)</option>
                <option value="multiply">Multiplikation (×)</option>
                <option value="divide">Division (÷)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="num2">Zweite Zahl:</label>
            <input type="number" id="num2" name="num2" step="any" required>
        </div>

        <button type="submit" class="btn btn-primary">Berechnen</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 