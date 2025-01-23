<?php
    require_once '../vendor/connect.php';
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /network/login");
        exit();
    }
    require_once '../vendor/updateinfo.php';
    $user = $_SESSION['user'];

    if (!($user['username'] == 'SkyBuilder1717')) {
        echo 'only developer!';
        http_response_code(403);
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete-comments'])) {
            if ($_POST['delete-comments']) {
                $query = mysqli_query($connect, "DELETE FROM Comments WHERE username NOT IN (SELECT username FROM Accounts WHERE state NOT IN ('banned', 'not_verified')) OR post_id NOT IN (SELECT id FROM Posts)");
                echo mysqli_affected_rows($connect) . ' comments has been deleted';
                exit();
            }
        }
        if (isset($_POST['delete-posts'])) {
            if ($_POST['delete-posts']) {
                $query = mysqli_query($connect, "DELETE FROM Posts WHERE author NOT IN (SELECT username FROM Accounts WHERE state NOT IN ('banned', 'not_verified'))");
                echo mysqli_affected_rows($connect) . ' posts has been deleted';
                exit();
            }
        }
        if (isset($_POST['delete-images'])) {
            if ($_POST['delete-images']) {
                $dir = "../vendor/uploads";
                $all = array_diff(scandir($dir), array('..', '.'));
                $sql = "SELECT files FROM Posts";
                $result = mysqli_query($connect, $sql);
                $used = [];
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $files = explode(',', $row['files']);
                        $used = array_merge($used, $files);
                    }
                }
                $deleted = 0;
                foreach ($all as $file) {
                    if (!in_array($file, $used) && file_exists($dir . '/' . $file) and !($file == ".htaccess")) {
                        unlink($dir . '/' . $file);
                        $deleted++;
                    }
                }
                echo $deleted . ' deleted files';
                exit();
            }
        }
        if (isset($_POST['delete-avatars'])) {
            if ($_POST['delete-avatars']) {
                $dir = "../vendor/avatar";
                $all = array_diff(scandir($dir), array('..', '.'));
                $sql = "SELECT avatar FROM Accounts";
                $result = mysqli_query($connect, $sql);
                $used = [];
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $files = explode(',', $row['avatar']);
                        $used = array_merge($used, $files);
                    }
                }
                $deleted = 0;
                foreach ($all as $file) {
                    if ((!in_array($file, $used) && file_exists($dir . '/' . $file)) and !($file == 'default-avatar.png')) {
                        unlink($dir . '/' . $file);
                        $deleted++;
                    }
                }
                echo $deleted . ' deleted avatars';
                exit();
            }
        }
        if (isset($_POST['submit-state'])) {
            if ($_POST['submit-state']) {
                $un = $_POST['username'];
                $state = $_POST['state'];
                $query = mysqli_query($connect, "UPDATE Accounts SET state = '$state' WHERE username = '$un'");
                echo $un . ' now have state: ' . $state;
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Admin panel</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
    </head>
    <body>
        <h1>dev panel</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="submit" name="delete-comments" value="clear comments"/>
            <input type="submit" name="delete-posts" value="clear posts"/>
            <br><br>
            <input type="submit" name="delete-images" value="delete unused files"/>
            <input type="submit" name="delete-avatars" value="delete unused avatars"/>
            <br><br>
            <select name="username" id="username" required>
                <?php
                    $result = mysqli_query($connect, "SELECT * FROM Accounts");
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['username'] . "'>" . base64_decode($row['name']) . " (@" . $row['username'] . ")</option>";
                        }
                    }
                ?>
            </select><input type="submit" name="submit-state" value="submit user state"/><br>
            <label for="state" style="font-size: 20px;">state:</label>
            <select name="state" id="state" required>
                <option value="developer">dev</option>
                <option value="admin">admin</option>
                <option value="mod">moderator</option>
                <option value="approved">verified</option>
                <option value="user">user</option>
                <option value="banned">banned</option>
                <option value="not_verified">not active</option>
            </select>
        </form>
    </body>
</html>