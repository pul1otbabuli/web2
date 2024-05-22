<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.

include("./utils.php");

header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
if (session_start() && !empty($_COOKIE[session_name()])) {
    $session_started = true;
    if (!empty($_SESSION['login'])) {
        header('Location: ./');
        exit();
    }
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form action="" method="POST" id="form">

        <label>
            Логин:<br>
            <input name="field-login">
        </label><br>

        <label>
            Пароль:
            <br>
            <input name="field-password" type="password">
        </label><br><br>
        <input type="submit" value="Отправить">
    </form>
</body>

</html>
    <?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
    $db = connect_to_db();

    $login = !empty($_POST['field-login']) ? $_POST['field-login'] : "";
    $pass_hash = !empty($_POST['field-password']) ? md5($_POST['field-password']) : "";

    $user_id = get_user_id($db, $login, $pass_hash);

    if ($user_id == -1) {
        header("Location: ./");
        exit();
    }
    else {
        if (!$session_started) {
            session_start();
        }
        // Если все ок, то авторизуем пользователя.
        $_SESSION['login'] = $login;
        // Записываем ID пользователя.
        $_SESSION['user_id'] = $user_id;

        // Делаем перенаправление.
        header('Location: ./');
    }
}
