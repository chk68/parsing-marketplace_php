<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
    <link rel="stylesheet" href="/src/public/Css/Confirmation.css">
</head>
<body>
<?php
require_once __DIR__ . '/../Model/DataManager.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $userId = isset($_GET['id']) ? $_GET['id'] : '';
    ?>
    <h2>Email Confirmation</h2>
    <form method="post" action="confirm-email.php">
        <label for="confirmation_code">Enter Confirmation Code:</label>
        <input type="text" id="confirmation_code" name="confirmation_code" required>
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
        <button type="submit" name="submit">Confirm</button>
    </form>
    <?php
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

    $enteredCode = $_POST["confirmation_code"];
    $userId = $_POST["user_id"];


    $isValidCode = $dataManager->checkConfirmationCodeById($userId, $enteredCode);

    if ($isValidCode) {

        $dataManager->confirmUserEmailById($userId);
        echo "Email successfully confirmed!";
    } else {
        echo "Invalid confirmation code. Please try again.";
    }
}
?>

</body>
</html>
