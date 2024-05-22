<?php

/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

include("./utils.php");

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();

    if (!empty($_COOKIE['save'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        // Выводим сообщение пользователю.
        $messages[] = 'Спасибо, результаты сохранены.';
        // Если в куках есть пароль, то выводим сообщение.
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных. ',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass'])
            );
        }
    }

    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['field-name-1'] = !empty($_COOKIE['name_error']);
    $errors['field-tel'] = !empty($_COOKIE['tel_error']);
    $errors['field-email'] = !empty($_COOKIE['email_error']);
    $errors['field-date'] = !empty($_COOKIE['date_error']);
    $errors['radio-group-2'] = !empty($_COOKIE['gender_error']);
    $errors['field-pl'] = !empty($_COOKIE['fpl_error']);
    $errors['field-bio'] = !empty($_COOKIE['bio_error']);
    $errors['check-1'] = !empty($_COOKIE['check_error']);

    // Выдаем сообщения об ошибках.
    if ($errors['field-name-1']) {
        setcookie('name_error', '', 100000);
        setcookie('name_value', '', 100000);
        $messages[] = '<div class="error">Заполните имя. Допустимые символы: буквы, пробелы, апострофы, дефис.</div>';
    }
    if ($errors['field-tel']) {
        setcookie('tel_error', '', 100000);
        setcookie('tel_value', '', 100000);
        $messages[] = '<div class="error">Заполните телефон. Допустимые символы: цифры.</div>';
    }
    if ($errors['field-email']) {
        setcookie('email_error', '', 100000);
        setcookie('email_value', '', 100000);
        $messages[] = '<div class="error">Заполните почту по правильному формату.</div>';
    }
    if ($errors['field-date']) {
        setcookie('date_error', '', 100000);
        setcookie('date_value', '', 100000);
        $messages[] = '<div class="error">Заполните дату рождения.</div>';
    }
    if ($errors['radio-group-2']) {
        setcookie('gender_error', '', 100000);
        setcookie('gender_value', '', 100000);
        $messages[] = '<div class="error">Заполните пол.</div>';
    }
    if ($errors['field-pl']) {
        setcookie('fpl_error', '', 100000);
        setcookie('fpl_value', '', 100000);
        $messages[] = '<div class="error">Выберете любимые ЯП из списка.</div>';
    }
    if ($errors['field-bio']) {
        setcookie('bio_error', '', 100000);
        setcookie('bio_value', '', 100000);
        $messages[] = '<div class="error">Заполните биографию.</div>';
    }
    if ($errors['check-1']) {
        setcookie('check_error', '', 100000);
        $messages[] = '<div class="error">Примите контракт.</div>';
    }

    // Складываем предыдущие значения полей в массив, если есть.
    // При этом санитизуем все данные для безопасного отображения в браузере.
    $values = array();
    $values['field-name-1'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
    $values['field-tel'] = empty($_COOKIE['tel_value']) ? '' : strip_tags($_COOKIE['tel_value']);
    $values['field-email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['field-date'] = empty($_COOKIE['date_value']) ? '' : strip_tags($_COOKIE['date_value']);
    $values['radio-group-2'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
    $values['field-pl'] = empty($_COOKIE['fpl_value']) ? '' : strip_tags($_COOKIE['fpl_value']);
    $values['field-bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);

    // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
    // ранее в сессию записан факт успешного логина.

    if (
        session_start() &&
        !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])
    ) {
        $messages[] = "<div>Вы вошли под логином " . $_SESSION['login'] . '<form action="./logout.php" method="POST"><button type="submit">Выйти</button></form></div>';
        $db = connect_to_db();
        $user_id = $_SESSION['user_id'];

        $submission = get_user_submission($db, $user_id);

        if (!empty($submission)) {
            $values["field-name-1"] = $submission[0]['name'];
            $values["field-tel"] = $submission[0]['phone'];
            $values["field-email"] = $submission[0]['email'];
            $values["field-date"] = $submission[0]['bdate'];
            $values["radio-group-2"] = $submission[0]['gender'] == "1"? "m" : "f";
            $values["field-bio"] = strip_tags($submission[0]['bio']);
            $values["field-pl"] = sprintf("@%s@", implode("@", $submission[0]['fpls']));
        }
        else {
            $messages[] = '<div class="error">Ошибка при получении данных из бд.</div>';
        }
    }

    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include ('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
    $db = connect_to_db();

    // Проверяем ошибки.
    $errors = FALSE;
    if (empty($_POST['field-name-1']) || strlen($_POST["field-name-1"]) > 150 || !preg_match("/^[\p{Cyrillic}a-zA-Z-' ]*$/u", $_POST["field-name-1"])) {
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('name_value', $_POST['field-name-1'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST['field-tel']) || !preg_match('/\A[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\Z/i', $_POST["field-tel"])) {
        setcookie('tel_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('tel_value', $_POST['field-tel'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST['field-email']) || !filter_var($_POST["field-email"], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('email_value', $_POST['field-email'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST['field-date'])) {
        setcookie('date_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('date_value', $_POST['field-date'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST['radio-group-2'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('gender_value', $_POST['radio-group-2'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST["field-pl"]) || count($_POST["field-pl"]) < 1 || !preg_match('/^((\Qpascal\E|\Qc\E|\Qcpp\E|\Qjs\E|\Qphp\E|\Qpython\E|\Qjava\E|\Qhaskel\E|\Qclojure\E|\Qprolog\E|\Qscala\E){1}[\,]{0,1})+$/i', implode(",", $_POST["field-pl"]))) {
        setcookie('fpl_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('fpl_value', sprintf("@%s@", implode("@", $_POST["field-pl"])), time() + 30 * 24 * 60 * 60);
    }
    setcookie('bio_value', $_POST['field-bio'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST["check-1"]) || $_POST["check-1"] != "accepted") {
        setcookie('check_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }

    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: ./index.php');
        exit();
    } else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('name_error', '', 100000);
        setcookie('tel_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('date_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('fpl_error', '', 100000);
        setcookie('check_error', '', 100000);
    }

    if (
        !empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])
    ) {
        try {
            $name = $_POST["field-name-1"];
            $phone = $_POST["field-tel"];
            $email = $_POST["field-email"];
            $bdate = $_POST["field-date"];
            $gender = $_POST["radio-group-2"] == "m" ? '1' : '0';
            $bio = empty($_POST["field-bio"]) ? '' : $_POST["field-bio"];
        } catch (Exception $e) {
            header("Location: ./index.php");
            exit();
        }
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE application
            SET name = :name, phone = :phone, email = :email, bdate = :bdate, gender = :gender, bio = :bio
            WHERE user_id = :user_id");
            $stmt->bindParam('user_id', $_SESSION['user_id']);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('phone', $phone);
            $stmt->bindParam('email', $email);
            $stmt->bindParam('bdate', $bdate);
            $stmt->bindParam('gender', $gender);
            $stmt->bindParam('bio', $bio);
            $stmt->execute();

            $stmt = $db->prepare("SELECT id from application WHERE user_id = :user_id");
            $stmt->bindParam("user_id", $_SESSION['user_id']);
            $stmt->execute();
            $row_id = $stmt->fetchAll()[0]['id'];

            $stmt = $db->prepare("DELETE FROM fpls WHERE parent_id = :parent_id");
            $stmt->bindParam('parent_id', $row_id);
            $stmt->execute();

            foreach ($_POST["field-pl"] as $fpl) {
                $stmt = $db->prepare(sprintf("INSERT INTO fpls (parent_id, fpl) VALUES (%s, :fpl);", $row_id));
                $stmt->bindParam('fpl', $fpl);
                $stmt->execute();
            }

            $db->commit();
        }
        catch (PDOException $e) {
            $db->rollback();
            header("Location: ./index.php");
            exit();
        }
    } else {
        // Генерируем уникальный логин и пароль.
        $login = uniqid();
        $pass = rand();
        // Сохраняем в Cookies.
        setcookie('login', $login);
        setcookie('pass', $pass);

        $pass_hash = md5($pass);

        try {
            $stmt = $db->prepare("INSERT INTO users (login, password_hash) VALUES (:login, :password_hash)");
            $stmt->bindParam('login', $login);
            $stmt->bindParam('password_hash', $pass_hash);
            $stmt->execute();

            $user_id = $db->lastInsertId();
        }
        catch (PDOException $e) {
            header("Location: ./index.php");
            exit();
        }
        try {
            $name = $_POST["field-name-1"];
            $phone = $_POST["field-tel"];
            $email = $_POST["field-email"];
            $bdate = $_POST["field-date"];
            $gender = $_POST["radio-group-2"] == "m" ? '1' : '0';
            $bio = empty($_POST["field-bio"]) ? '' : $_POST["field-bio"];
        } catch (Exception $e) {
            setcookie("saving_status", "-3");
            return;
        }

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO application
            (user_id, name, phone, email, bdate, gender, bio)
            VALUES (:user_id, :name, :phone, :email, :bdate, :gender, :bio);");
            $stmt->bindParam('user_id', $user_id);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('phone', $phone);
            $stmt->bindParam('email', $email);
            $stmt->bindParam('bdate', $bdate);
            $stmt->bindParam('gender', $gender);
            $stmt->bindParam('bio', $bio);
            $stmt->execute();

            $submition_rowid = $db->lastInsertId();
            foreach ($_POST["field-pl"] as $fpl) {
                $stmt = $db->prepare(sprintf("INSERT INTO fpls (parent_id, fpl) VALUES (%s, :fpl);", $submition_rowid));
                $stmt->bindParam('fpl', $fpl);
                $stmt->execute();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            return;
        }
    }

    // Сохраняем куку с признаком успешного сохранения.
    setcookie('save', '1');

    // Делаем перенаправление.
    header('Location: ./');
}
