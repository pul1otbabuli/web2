<?php

$status_message = array(
    "1" => "Спасибо, результаты сохранены.",
    "-1" => "Некорректный формат имени.",
    "-2" => "Некорректный формат телефона.",
    "-3" => "Некорректный формат почты.",
    "-4" => "Заполните дату рождения",
    "-5" => "Заполните пол.",
    "-6" => "Заполните любимые языки программирования.",
    "-7" => "Заполните поле биографии.",
    "-8" => "Примите политику конфиденциальности."
);

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print($status_message[$_GET['save']]);
    }

    include('form.php');
    exit();
}
$status = 1;
$errors = FALSE;
if (empty($_POST['field-name-1']) || strlen($_POST["field-name-1"]) > 150 || !preg_match("/^[\p{Cyrillic}a-zA-Z-' ]*$/u", $_POST["field-name-1"])) {
    print('Заполните имя.<br/>');
    $status = $status == 1 ? -1 : $status;
    $errors = TRUE;
}
if (empty($_POST['field-tel']) || !preg_match('/[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}/i', $_POST["field-tel"])) {
    print('Заполните телефон.<br/>');
    $status = $status == 1 ? -2 : $status;
    $errors = TRUE;
}
if (empty($_POST['field-email']) || !filter_var($_POST["field-email"], FILTER_VALIDATE_EMAIL)) {
    print('Заполните почту.<br/>');
    $status = $status == 1 ? -3 : $status;
    $errors = TRUE;
}
if (empty($_POST['field-date'])) {
    print('Заполните дату.<br/>');
    $status = $status == 1 ? -4 : $status;
    $errors = TRUE;
}
if (empty($_POST['radio-group-2'])) {
    print('Заполните пол.<br/>');
    $status = $status == 1 ? -5 : $status;
    $errors = TRUE;
}
if (empty($_POST['field-name'])) {
    print('Заполните любимые ЯП.<br/>');
    $status = $status == 1 ? -6 : $status;
    $errors = TRUE;
}
if (empty($_POST['field-bio'])) {
    print('Заполните биографию.<br/>');
    $status = $status == 1 ? -7 : $status;
    $errors = TRUE;
}
if (empty($_POST['check-1'])) {
    print('Согласитесь с политикой конфиденциальности.<br/>');
    $status = $status == 1 ? -8 : $status;
    $errors = TRUE;
}


if ($errors) {
    header(sprintf('Location: ?save=%d', $status));
    exit();
}


$user = 'u67294';
$pass = '3387363';
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
    $stmt->bindParam('gender', $_POST['radio-group-2']);
    $stmt->bindParam('bio', $_POST['field-bio']);
    $stmt->execute();

    $last_id = $db->lastInsertId();
    foreach ($_POST["field-name"] as $fpl) {
        $stmt = $db->prepare(sprintf("INSERT INTO favoritelang (parent_id, fpl) VALUES (%s, :fpl);", $last_id));
        $stmt->bindParam('fpl', $fpl);
        $stmt->execute();
    }
} catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');
