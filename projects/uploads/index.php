<?php
    require_once "../../vendor/check.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uploads/</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #343a40;
        }
        table {
            border-radius: 6px;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background-color: #e9ecef;
        }
        .delete-button {
            position: relative;
            border-radius: 7px;
            top: 6px;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
        }
        .add-btn {
            position: relative;
            border-radius: 7px;
            top: 10px;
            background-color: #00f00a;
            border: none;
            padding: 5px 10px;
        }
        .add-btn a {
            color: white;
            text-decoration: none;
        }
        .delete-button:hover {
            background-color: #c82333;
            cursor: pointer;
        }
        .add-btn:hover {
            background-color: #00a00f;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
        $directory = '../uploads';
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));

        if (isset($_POST['delete'])) {
            foreach ($_POST['files'] as $file) {
                if (file_exists($directory . '/' . $file)) {
                    unlink($directory . '/' . $file);
                }
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $file_dates = [];
        foreach ($files as $file) {
            $file_dates[$file] = filectime($directory . '/' . $file);
        }

        $sorted_files = array_keys($file_dates);
    ?>
    <h1>uploads/</h1>
    <?php if (count($sorted_files) > 0): ?>
        <form method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Выбрать</th>
                        <th>Имя файла</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sorted_files as $file): ?>
                        <?php if ($file != "index.php"): ?>
                            <tr>
                                <td><input type="checkbox" name="files[]" value="<?php echo htmlspecialchars($file); ?>"></td>
                                <td><a href="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars($file); ?></a></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="delete" class="delete-button">Удалить выбранные файлы</button>
        </form>
    <?php else: ?>
        <p>Нет доступных файлов.</p>
    <?php endif; ?>
    <button name="add" class="add-btn"><a href="/projects/load">Добавить новый файл</a></button>
</body>
</html>