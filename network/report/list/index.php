<?php
    require_once '../../vendor/connect.php';
    require_once '../../vendor/antinull.php';
    require_once '../../vendor/mailclass.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }
    require_once '../../vendor/updateinfo.php';
    $user = $_SESSION['user'];
    $email = $user['email'];

    if (!(($user['state'] == 'mod') or ($user['state'] == 'admin') or ($user['state'] == 'developer'))) {
        header("Location: /network/report");
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['cancel'])) {
            $id = $_POST['cancel'];
            $emailer = $_POST['email'];
            $mail = new SendMail('mappercoder@yandex.com', 'efkfcibrrtirlzlx', 'ssl://smtp.yandex.ru', 465, "UTF-8");
            $from = array("mappercoder", "mappercoder@yandex.com");
            $result =  $mail->send($emailer, 'Репорт №' . $id, "<strong>Ваш репорт с ID: <i>$id</i> был отклонён.</strong><br><br>Модерация Mapperter к сожалению отклонила ваш репорт,<br>так как посчитала его недействительным, и удалила.<br><br>Попробуйте в следующий раз!", $from);
            $query = mysqli_query($connect, "DELETE FROM Reports WHERE id = '$id'");
        }
        if (isset($_POST['accept'])) {
            $id = $_POST['accept'];
            $emailer = $_POST['email'];
            $mail = new SendMail('mappercoder@yandex.com', 'efkfcibrrtirlzlx', 'ssl://smtp.yandex.ru', 465, "UTF-8");
            $from = array("mappercoder", "mappercoder@yandex.com");
            $result =  $mail->send($emailer, 'Репорт №' . $id, "<strong>Ваш репорт с ID: <i>$id</i> был одобрен!</strong><br><br>Модерация Mapperter одобрила ваш репорт,<br>и скоро устранит все возникшие проблемы в ближайшее время!<br><br>Просто подождите!", $from);
            $query = mysqli_query($connect, "DELETE FROM Reports WHERE id = '$id'");
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=2.0">
        <title>Mapperter: Reports list</title>
        <link rel="icon" type="image/png" href="/favicon.png">
        <meta name="robots" content="noindex, nofollow">
        <style>
            body {
                background-image: url('/img/backgrounds/default.png');
                background-repeat: repeat;  
                background-position: center;
                margin: 0; 
                padding: 0; 
                font-family: Arial, sans-serif; 
            }
            .header {
                background-color: rgba(0, 0, 0, 0)fff; 
                padding: 20px;
                border-radius: 10px;
                text-align: center;
                text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
            }
            .header__logo {
                font-size: 24px;
                font-weight: bold;
                color: #fff; 
                margin-bottom: 10px;
            }
            .report-box {
                background-color: #ffffff;
                border-radius: 16px;
                padding: 16px;
                width: auto;
                max-width: 900px;
                margin: 20px auto;
            }
            .box {
                padding: 10px 5px;
            }
            .report {
                border-radius: 4px;
                border: 2px solid;
            }
            footer p {
                color: white;
            }
            button {
                border-radius: 4px;
                height: 30px;
                border: none;
                color: white;
                background-color: #afafaf;
                cursor: pointer;
            }
            form {
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="header__logo">Репорты</div>
        </div>
        <main class="report-box">
            <?php
                $query = "SELECT * FROM Reports";
                $result = mysqli_query($connect, $query);

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $nemail = $row['email'];

                            echo '<div class="box">';
                            echo '<div class="report">';
                            echo '<h2>' . base64_decode($row['name']) . '</h2>';
                            echo '<p>' . nl2br(htmlspecialchars(base64_decode($row['description']))) . '</p>';
                            if (!($row['files'] == '')) {
                                echo '<img src="/network/report/uploads/' . $row['files'] . '" style="display: inline-block; max-height: 315px; padding: 5px 0;" />';
                            }

                            echo '<form action="" method="POST" enctype="multipart/form-data">';
                            echo '<button type="submit" name="cancel" value="' . $row['id'] . '"><img src="/img/delete.png" width="16px" height="16px" /> Отклонить</button><br>';
                            echo '<input type="hidden" id="email" name="email" value="' . $nemail . '" />';
                            echo '</form>';

                            echo '<form action="" method="POST" enctype="multipart/form-data">';
                            echo '<button type="submit" name="accept" value="' . $row['id'] . '"><img src="/img/icons/approved.png" width="16px" height="16px" /> Одобрить</button>';
                            echo '<input type="hidden" id="email" name="email" value="' . $nemail . '" />';
                            echo '</form>';


                            $account = mysqli_query($connect, "SELECT * FROM Accounts WHERE email = '$nemail'");
                            $account = mysqli_fetch_assoc($account);

                            echo '<p>Автор: ' . $account['username'] . '</p>';

                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>На сегодня нету репортов!</p>";
                    }
                } else {
                    echo "<p>Не удалось загрузить репорты.</p>";
                }
            ?>
        </main>
        <div class="header">
            <footer>
                <p>Важно помнить о правилах платформы!</p>
                <p>-SkyBuilder- © 2024</p>
            </footer>
        </div>
    </body>
</html>