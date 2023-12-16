<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connect = mysqli_connect($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
if (mysqli_connect_errno()) {
    printf("Ошибка подключения: %s\n", mysqli_connect_error());
    exit();
}
mysqli_query($connect, "SET NAMES utf8");

$result = mysqli_query($connect, "SELECT * FROM ads_info");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список объявлений</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            color: #0000FF;
            text-decoration: underline;
            cursor: pointer;
        }

        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<form method="post">
    <label for="user_email">Введите ваш email: </label>
    <input type="email" id="user_email" name="user_email" required>
    <button type="submit">Получить объявления</button>
</form>

<?php if ($result && mysqli_num_rows($result) > 0) : ?>
    <h2>Список объявлений:</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Ad ID</th>
            <th>URL</th>
            <th>Price</th>
            <th>Mail</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['ad_id'] ?></td>
                <td><a href='<?= $row['ad_url'] ?>' target='_blank'><?= $row['ad_url'] ?></a></td>
                <td><?= $row['ad_price'] . " UAH"?></td>
                <td><?= $row['user_email'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($result) : ?>
    <p>Нет объявлений для данного email</p>
<?php else : ?>
    <p>Ошибка запроса: <?= mysqli_error($connect) ?></p>
<?php endif; ?>

<a href='index.php'>На главную</a>
</body>
</html>

<?php
mysqli_close($connect);
?>
