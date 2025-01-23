<style>
    .posts {
        padding: 35px;
        max-width: 380px;
        width: auto;
    }
    .posts .post {
        margin: 50px 0;
        padding: 20px;
        width: auto;
        max-width: 390px;
        height: auto;
        max-height: none;
        box-shadow: 0 7px 5px rgba(0, 0, 0, 0.3);
        border-radius: 20px;
        color: white;
        border: none;
        background-color: #1a1a1a;
    }
    .posts .post a {
        color: white;
        text-decoration: none;
        text-align: left;
    }
    .posts .post p .link {
        text-decoration: underline;
        color: #00f;
    }
    .posts .post p .username {
        text-decoration: none;
        color: #0cf;
    }
    .posts .post .avatar p {
        padding: 0 14px;
    }
    .posts .post .files img,
    .posts .post .files video {
        max-width: 350px;
    }
    .posts .post .files a {
        text-decoration: underline;
        color: #0ff;
    }
    .posts .post .avatar {
        display: flex;
        justify-content: left;
    }
    .posts .post button {
        display: flex;
        text-align: right;
        align-items: right;
        justify-content: right;
        padding: 3px;
        border-radius: 15px;
        cursor: pointer;
        border: none;
        height: 22px;
    }
    .posts .post button[name="edit-post"] {
        width: 115px;
    }
    .posts .post button[id="comment"] {
        width: 140px;
    }
    .posts .post .buttons form {
        padding: 5px 0;
    }
    .posts .post .comments {
        padding: 10px 0;
    }
    .posts .post .comments .avatar {
        padding: 5px;
        text-align: left;
        align-items: left;
        justify-content: left;
        height: auto;
        width: auto;
        max-width: 80%;
        max-height: none;
    }
    .posts .post .comments p {
        padding: 0 10px;
        color: white;
        width: 350px;
    }
    .posts .post .comments .avatar a {
        display: flex;
        color: white;
        padding: 0 10px;
        font-size: 15px;
        justify-content: space-between;
        font-weight: bold;
    }
    .posts .post .comments .comment {
        border-radius: 6px;
        border: 1px solid #fff;
        padding: 10px 0;
        width: auto;
    }
    .posts .post footer p a {
        color: #f80000;
    }
    .posts .post .delete-comment-btn {
        width: 100%;
        margin-right: 10px;
        display: flex;
        text-align: right;
        align-items: right;
        justify-content: right;
    }
    .posts .post .delete-comment-btn button {
        display: inline-block;
        text-align: center;
        background-color: #fcc;
        border-radius: 5px;
        width: auto;
        height: 20px;
    }
    .posts .post .delete-comment-btn button img {
        display: flex;
        text-align: center;
        align-items: center;
        justify-content: center;
    }
    .posts .post .buttons .likes {
        height: 20px;
        width: 100%;
        display: inline-block;
    }
    .posts .post .buttons .likes button {
        display: inline-block;
    }
    .posts .post .buttons p {
        display: flex;
        text-align: right;
        align-items: right;
        justify-content: right;
    }
    .posts .post .post .avatar img[name="avatar"] {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
<?php
    require_once "../vendor/connect.php";
    session_start();

    function getLikesText($count) {
        if ($count % 10 == 1 && $count % 100 != 11) {
            return "$count лайк";
        } elseif ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20)) {
            return "$count лайка";
        } else {
            return "$count лайков";
        }
    }

    function convertTimestamp($timestamp) {
        date_default_timezone_set('Europe/Moscow');
        return date('Y-m-d H:i:s', (strtotime($timestamp)) + (10 * 3600));
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete-post'])) {
            if ($_POST['delete-post']) {
                $value = $_POST['delete-post'];
                $query = mysqli_query($connect, "DELETE FROM Posts WHERE id = '$value'");
            }
        }
        if (isset($_POST['delete-comment'])) {
            if ($_POST['delete-comment']) {
                $value = $_POST['delete-comment'];
                $query = mysqli_query($connect, "DELETE FROM Comments WHERE id = '$value'");
            }
        }
        if (isset($_POST['like'])) {
            if ($_POST['like']) {
                $value = $_POST['like'];
                $islikedQuery = 'SELECT * FROM Likes WHERE post_id="' . $value . '" AND username="' . $_SESSION['user']['username'] . '"';
                $islikedResult = mysqli_query($connect, $islikedQuery);
                if (mysqli_num_rows($islikedResult) > 0) {
                    $insert = mysqli_query($connect, "DELETE FROM Likes WHERE post_id = '$value' AND username = '$username'");
                } else {
                    $insert = mysqli_query($connect, "INSERT INTO Likes (post_id, username) VALUES ('$value', '$username')");
                }
            }
        }
        if (isset($_POST['pin'])) {
            if ($_POST['pin']) {
                $value = $_POST['pin'];
                $insert = mysqli_query($connect, "UPDATE Posts SET is_pinned='1' WHERE id='$value'");
            }
        }
        if (isset($_POST['unpin'])) {
            if ($_POST['unpin']) {
                $value = $_POST['unpin'];
                $insert = mysqli_query($connect, "UPDATE Posts SET is_pinned='NULL' WHERE id='$value'");
            }
        }
    }

    if ($is_user_profile) {
        $query = "SELECT * FROM Posts WHERE author='" . $user['username'] . "' ORDER BY is_pinned DESC, created_at DESC ";
    } else {
        function convertLinksToHTML($text) {
            $pattern = '/(http|https):\/\/[a-zA-Z0-9.-\/?=&:#@_]*/';
            $userpattern = '/@([a-zA-Z0-9]+)/';
            $text = preg_replace($pattern, '<a class="link" href="$0">$0</a>', $text);
            $text = preg_replace($userpattern, '<a class="username" href="/network/user/$1">@$1</a>', $text);
            return nl2br($text);
        }

        $query = "SELECT * FROM Posts ORDER BY is_pinned DESC, created_at DESC LIMIT 30";
    }
    $result = mysqli_query($connect, $query);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $name = base64_decode($row['name']);
                $description = base64_decode($row['description']);
                $un = $row['author'];
                $files = !empty($row['files']) ? explode(',', $row['files']) : [];

                $author = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM Accounts WHERE username = '$un'"));

                if (isset($author) and !($author['state'] == "banned" or $author['state'] == "not_verified")) {
                    if ($author['state'] == "mod") {
                        $statetitle = "Модератор";
                    } elseif ($author['state'] == "admin") {
                        $statetitle = "Администратор";
                    } elseif ($author['state'] == "approved") {
                        $statetitle = "Доверенный пользователь";
                    } elseif ($author['state'] == "developer") {
                        $statetitle = "Разработчик";
                    }
                    echo '<div class="post">';
                    if ($row["is_pinned"]) {
                        echo '<p>Закреплено</p>';
                    }
                    echo '<div class="avatar">';
                    echo '<img src="/network/vendor/avatar/' . htmlspecialchars($author['avatar']) . '" name="avatar" />';

                    echo '<p><strong><a href="/network/user/' . htmlspecialchars($author['username']) . '"';
                    $author_premium = mysqli_query($connect, "SELECT * FROM Premium WHERE username = '$un' AND expires > NOW()");
                    if (mysqli_num_rows($author_premium) > 0) {
                        echo ' style="color: yellow;"';
                    }
                    echo '>' . htmlspecialchars(base64_decode($author['name'])) . '</a></strong>';
                    if (mysqli_num_rows($author_premium) > 0) {
                        echo '<img src="/img/icons/premium.png" title="Premium подписка" alt="Premium значок" style="max-height: 20px;" class="icon" />';
                    }
                    if (!($author['state'] == "not_verified" or $author['state'] == "banned" or $author['state'] == "user")) {
                        echo '<img src="/img/icons/' . htmlspecialchars($author['state']) . '.png" title="' . $statetitle . '" alt="' . htmlspecialchars($author['state']) . '" style="max-height: 20px;" class="icon" />';
                    }
                    echo '</p>';

                    if (!($_SESSION['user']['state'] == "not_verified" or $_SESSION['user']['state'] == "banned")) {
                        echo '<div class="buttons">';
                        if (($_SESSION['user']['username'] == $author['username']) or ($_SESSION['user']['state'] == "admin") or ($_SESSION['user']['state'] == "developer")) {
                            echo '<form action="/network/editpost.php" method="GET">';
                            echo '<button type="submit" name="id" value="' . $row['id'] . '">Редактировать</button>';
                            echo '</form>';
                            echo '<form action="" method="POST" enctype="multipart/form-data">';
                            echo '<button type="submit" name="delete-post" value="' . $row['id'] . '"><img src="/img/delete.png" width="16px" height="16px" /></button>';
                            echo '</form>';
                        }
                        if ($_SESSION['user']['state'] == 'developer') {
                            if (!($row["is_pinned"])) {
                                echo '<form action="" method="POST" enctype="multipart/form-data">';
                                echo '<button type="submit" name="pin" value="' . $row['id'] . '">Закрепить</button>';
                                echo '</form>';
                            } else {
                                echo '<form action="" method="POST" enctype="multipart/form-data">';
                                echo '<button type="submit" name="unpin" value="' . $row['id'] . '">Открепить</button>';
                                echo '</form>';
                            }
                        }
                            echo '</div>';
                    }
                    echo '</div>';
                    if ($row['is_changed'] > 0) {
                        echo '<p>(Изменено)</p>';
                    }
                    echo '<h2>' . htmlspecialchars($name) . '</h2>';
                    echo convertLinksToHTML('<p>' . htmlspecialchars($description) . '</p>');

                    if (!empty($files)) {
                        echo "<div class='files'>";
                        foreach ($files as $file) {
                            $info = pathinfo('../vendor/uploads/' . $file);
                            $ext = strtolower($info['extension']);

                            $allow = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'catrobat', 'ccode'];
                            $images = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
                            $games = ['catrobat', 'ccode'];

                            if (in_array($ext, $allow)) {
                                if (in_array($ext, $images)) {
                                    echo '<img src="/network/vendor/uploads/' . $file . '" />';
                                } elseif (in_array($ext, $games)) {
                                    echo '<p><img src="/projects/file_icon.png" title="' . $file . '" style="max-height: 20px;" /> ' . $ext . ' файл <a href="/network/vendor/uploads/' . $file . '">Скачать</a></p>';
                                }
                            } else {
                                echo "<a href='/network/vendor/uploads/$file' target='_blank'>" . htmlspecialchars($file) . "</a><br>";
                            } 
                        }
                        echo "</div>";
                    }
                    echo '<h3 style="font-size: 15px; text-decoration: underline;">Создан в ' . htmlspecialchars(convertTimeStamp($row["created_at"])) . '</h3>';

                    // Other buttons
                    echo '<div class="buttons">';
                    $LikesResult = mysqli_query($connect, 'SELECT * FROM Likes WHERE post_id="' . $row['id'] . '"');
                    echo '<div class="likes">';
                    $queryLike = 'SELECT * FROM Likes WHERE post_id="' . $row['id'] . '" AND username="' . $_SESSION['user']['username'] . '"';
                    $LikeResult = mysqli_query($connect, $queryLike);
                    if (mysqli_fetch_assoc($LikeResult))
                    {
                        $likeee = 'Дизлайкнуть';
                    } else {
                        $likeee = 'Лайкнуть';
                    }
                    echo '<form action="" method="POST" enctype="multipart/form-data">';
                    if (!($_SESSION['user']['state'] == "not_verified" or $_SESSION['user']['state'] == "banned")) {
                        echo '<button type="submit" id="like" name="like" value="' . $row['id'] . '">' . $likeee . '</button>';
                    }
                    echo '</form>';
                    echo '<p>' . getLikesText(mysqli_num_rows($LikesResult)) . '</p>';
                    echo '</div>';
                    if ((!($row["is_pinned"])) and (!($_SESSION['user']['state'] == "not_verified" or $_SESSION['user']['state'] == "banned"))) {
                        echo '<form action="/network/comment.php" method="GET">';
                        echo '<button type="submit" id="comment" name="id" value="' . $row['id'] . '">Прокомментировать</button>';
                        echo '</form>';
                    }
                    echo '</div>';

                    if (!($row["is_pinned"])) {
                        echo '<div class="comments">';
                        $queryComments = 'SELECT * FROM Comments WHERE post_id="' . $row['id'] . '" ORDER BY "created_at" ASC';
                        $commentsResult = mysqli_query($connect, $queryComments);
                        if ($commentsResult) {
                            if (mysqli_num_rows($commentsResult) > 0) {
                                while ($row = mysqli_fetch_assoc($commentsResult)) {
                                    $resultComment = mysqli_query($connect, 'SELECT * FROM Accounts WHERE username="' . $row['username'] . '"');
                                    $commentater = mysqli_fetch_assoc($resultComment);
                                    if ($commentater['state'] == "mod") {
                                        $commentatertitlestate = "Модератор";
                                    } elseif ($commentater['state'] == "admin") {
                                        $commentatertitlestate = "Администратор";
                                    } elseif ($commentater['state'] == "approved") {
                                        $commentatertitlestate = "Доверенный пользователь";
                                    } elseif ($commentater['state'] == "developer") {
                                        $commentatertitlestate = "Разработчик";
                                    }
                                    if (isset($commentater) and !($commentater['state'] == "banned" or $commentater['state'] == "not_verified")) {
                                        echo '<div class="comment">';
                                        echo '<div class="avatar">';
                                        echo '<img src="/network/vendor/avatar/' . $commentater['avatar'] . '" name="avatar" />';
                                        
                                        echo '<a href="/network/user/' . htmlspecialchars($commentater['username']) . '"';
                                        $comment_premium = mysqli_query($connect, 'SELECT * FROM Premium WHERE username = "' . $commentater['username'] . '" AND expires > NOW()');
                                        if (mysqli_num_rows($comment_premium) > 0) {
                                            echo ' style="color: yellow;"';
                                        }
                                        echo '>' . htmlspecialchars(base64_decode($commentater['name']));
                                        if (mysqli_num_rows($comment_premium) > 0) {
                                            echo '<img src="/img/icons/premium.png" title="Premium подписка" alt="Premium значок" style="max-height: 20px;" class="icon" />';
                                        }
                                        if (!($commentater['state'] == "not_verified" or $commentater['state'] == "banned" or $commentater['state'] == "user")) {
                                            echo '<img src="/img/icons/' . htmlspecialchars($commentater['state']) . '.png" title="' . $commentatertitlestate . '" alt="' . htmlspecialchars($commentater['state']) . '" style="max-height: 20px;" class="icon" />';
                                        }
                                        echo '</a>';

                                        echo '</div>';
                                        echo convertLinksToHTML('<p>' . htmlspecialchars(base64_decode($row['description'])) . '</p>');
                                        if (!($_SESSION['user']['state'] == "not_verified" or $_SESSION['user']['state'] == "banned")) {
                                            if (($_SESSION['user']['username'] == $commentater['username']) or ($_SESSION['user']['state'] == "admin") or ($_SESSION['user']['state'] == "developer")) {
                                                echo '<div class="delete-comment-btn">';
                                                echo '<form action="" method="POST" enctype="multipart/form-data">';
                                                echo '<button type="submit" name="delete-comment" value="' . $row['id'] . '"><img src="/img/delete.png" width="16px" height="16px" /></button>';
                                                echo '</form>';
                                                echo '</div>';
                                            }
                                        }
                                        echo '</div>';
                                    }
                                }
                            }
                        echo '</div>';
                        }
                    }

                    echo "</div>";
                }
            }
        } else {
            echo "<p>Нету постов на сегодня.</p>";
        }
    } else {
        echo "<p>Не удалось загрузить посты.</p>";
    }
?>