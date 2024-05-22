<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();

    // если есть запись в куке, что все ок, выводим сообщение пользователю
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
    }

    // забираем из куки информацию об ошибках
    $errors = array();
    $errors['field-name-1'] = !empty($_COOKIE['name_error']);
    $errors['field-tel'] = !empty($_COOKIE['tel_error']);
    $errors['field-email'] = !empty($_COOKIE['email_error']);
    $errors['field-date'] = !empty($_COOKIE['date_error']);
    $errors['radio-group-2'] = !empty($_COOKIE['gender_error']);
    $errors['field-pl'] = !empty($_COOKIE['fpl_error']);
    $errors['field-bio'] = !empty($_COOKIE['bio_error']);
    $errors['check-1'] = !empty($_COOKIE['check_error']);

    // смотрим, есть ли ошибки по полям, удаляем куки, выводим сообщение пользователю
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

    // забираем значения полей из куки
    $values = array();
    $values['field-name-1'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
    $values['field-tel'] = empty($_COOKIE['tel_value']) ? '' : $_COOKIE['tel_value'];
    $values['field-email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['field-date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
    $values['radio-group-2'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['field-pl'] = empty($_COOKIE['fpl_value']) ? '' : $_COOKIE['fpl_value'];
    $values['field-bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];

    // выводим форму пользователю
    include ('form.php');
} else {

    // валидируем поля, записываем в куки информацию об ошибках и значения полей
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
    setcookie('fpl_value', sprintf("@%s@", implode("@", $_POST["field-pl"])), time() + 30 * 24 * 60 * 60);
    if (empty($_POST["check-1"]) || $_POST["check-1"] != "accepted") {
        setcookie('check_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }

    // если валидация не прошла, то перезагружаем страницу и выходим, иначе удаляем куки об ошибках
    if ($errors) {
        header('Location: index.php');
        exit();
    } else {
        setcookie('name_error', '', 100000);
        setcookie('tel_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('date_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('fpl_error', '', 100000);
        setcookie('check_error', '', 100000);
    }


    // записываем информацию в базу данных
    include ("./db_data.php");
    $db = new PDO(
        'mysql:host=localhost;dbname=u67441',
        $user,
        $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );


    try {
        $stmt = $db->prepare("INSERT INTO application
        (name, phone, email, bdate, gender, bio)
        VALUES (:name, :phone, :email, :bdate, :gender, :bio);");
        $stmt->bindParam('name', $_POST['field-name-1']);
        $stmt->bindParam('phone', $_POST['field-tel']);
        $stmt->bindParam('email', $_POST['field-email']);
        $stmt->bindParam('bdate', $_POST['field-date']);
        $gender = $_POST["radio-group-2"] == "m" ? '1' : '0';
        $stmt->bindParam('gender', $gender);
        $stmt->bindParam('bio', $_POST['field-bio']);
        $stmt->execute();

        $last_id = $db->lastInsertId();
        foreach ($_POST["field-pl"] as $fpl) {
            $stmt = $db->prepare(sprintf("INSERT INTO favoritelang (parent_id, fpl) VALUES (%s, :fpl);", $last_id));
            $stmt->bindParam('fpl', $fpl);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        print ('Error : ' . $e->getMessage());
        exit();
    }

    // записываем в куку, что все окей
    setcookie('save', '1');

    // перезагружаем страницу
    header('Location: index.php');
}
