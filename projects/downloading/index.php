<link rel="icon" type="image/png" href="/favicon.png">
<title>Скачивание...</title>
<?php
if (!isset($_GET['file'])) {
    die('Файл не указан.');
}

$file = $_GET['file'];
$directory = '../uploads';

if (!file_exists($directory . '/' . $file)) {
    die('Файл не найден.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($directory . '/' . $file));

readfile($directory . '/' . $file);
exit;
?>
