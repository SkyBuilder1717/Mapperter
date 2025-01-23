<?php
    require_once "../../vendor/check.php";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка файлов</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <style>
        body {
            background-color: white;
            font-family: "Gill Sans", sans-serif;
        }
        button {
            border: none;
            width: 100px;
            border-radius: 2px;
            background-color: #a1a1a1;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Загрузить новый файл</h1>
    <form action="uploading.php" method="post" enctype="multipart/form-data">
        <label for="file">Выберите файл для загрузки:</label><br>
        <input type="file" name="file" id="file" required><br><br>
        <button type="submit">Загрузить</button>
    </form>
</body>
</html>