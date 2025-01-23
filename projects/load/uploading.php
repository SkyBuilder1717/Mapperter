<?php
    require_once "../../vendor/check.php";
?>
<link rel="icon" type="image/png" href="/favicon.png">
<title>Загрузка файлов</title>
<?php
$directory = '../uploads';

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Ошибка загрузки файла.');
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        die('Файл слишком большой. Максимальный размер файла — 10MB.');
    }

    $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    //$fileName = uniqid() . '.' . $fileExt;
    $fileName = $file["name"];
    $filePath = $directory . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        echo 'Файл успешно загружен.';
    } else {
        die('Ошибка при перемещении загруженного файла.');
    }
} else {
    die('Файл не был загружен.');
}
?>