<?php
    require_once "connect.php";
    require_once "updateinfo.php";
    session_start();

    $username = $_SESSION['user']['username'];

    $emojiFeature = mysqli_query($connect, "SELECT * FROM Features WHERE feature='emoji' AND username='$username'");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        if (strlen($name) == 0) {
            $name = 'Пост';
        } else {
            $name = limitString($name, 32);
        }
        $name = mysqli_real_escape_string($connect, $name);
        $desc = $_POST['description'];
        if (!(strlen($desc) == 0)) {
            $desc = limitString($desc, 255);
        }
        $desc = mysqli_real_escape_string($connect, $desc);
        if (mysqli_num_rows($emojiFeature) == 0) {
            $name = removeEmojis($name);
            $desc = removeEmojis($desc);
        }
        $name = base64_encode($name);
        $desc = base64_encode($desc);

        $files = array();
        $upload_dir = 'uploads/';

        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            $total_size = 0;

            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                $file = $_FILES['files'];

                $total_size += $file['size'][$key];

                if ($total_size > (1024 * 1024 * 40)) {
                    die('Файл слишком большой. Максимальный размер файла — 40MB. (' . $total_size . ' байт)');
                }

                if ($file['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                    $file_name = basename($file['name'][$key]);
                    $file_tmp = $file['tmp_name'][$key];

                    if ($file['error'][$key] !== UPLOAD_ERR_OK) {
                        die('Ошибка при загрузке файла: ' . $file['error'][$key]);
                    }

                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $unique_name = generateString(16) . '.' . $file_ext;

                    if (move_uploaded_file($file_tmp, $upload_dir . $unique_name)) {
                        $files[] = $unique_name;
                    } else {
                        die('Не удалось переместить файл ' . $file_name);
                    }
                }
            }

            if (!empty($files)) {
                $files_string = implode(',', $files);
            } else {
                $files_string = '';
            }
        }

        $post = "INSERT INTO Posts (name, description, author, files) VALUES ('$name', '$desc', '$username', '$files_string')";
        mysqli_query($connect, $post);
        header("Location: /network/main");
        exit();
    } else {
        header("Location: /network/main");
        exit();
    }
?>
