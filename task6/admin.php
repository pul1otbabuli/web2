<?php

include("./utils.php");

$db = connect_to_db();

if (
    empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    count(get_admin_db_data($db, $_SERVER['PHP_AUTH_USER'], md5($_SERVER['PHP_AUTH_PW']))) == 0
)
{
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $submissions = get_form_submissions($db);
    $fpls_count = count_fpls($submissions);

    include("./admin_page.php");
}
else {
    if ($_POST['button-action'] == "EDIT") {
        $errors = FALSE;
        if (empty($_POST['field-name-1']) || strlen($_POST["field-name-1"]) > 150 || !preg_match("/^[\p{Cyrillic}a-zA-Z-' ]*$/u", $_POST["field-name-1"])) {
            $errors = TRUE;
        }
        if (empty($_POST['field-tel']) || !preg_match('/\A[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\Z/i', $_POST["field-tel"])) {
            $errors = TRUE;
        }
        if (empty($_POST['field-email']) || !filter_var($_POST["field-email"], FILTER_VALIDATE_EMAIL)) {
            $errors = TRUE;
        }
        if (empty($_POST['field-date'])) {
            $errors = TRUE;
        }
        if (empty($_POST['radio-group-2'])) {
            $errors = TRUE;
        }
        if (empty($_POST["field-pl"]) || count($_POST["field-pl"]) < 1 || !preg_match('/^((\Qpascal\E|\Qc\E|\Qcpp\E|\Qjs\E|\Qphp\E|\Qpython\E|\Qjava\E|\Qhaskel\E|\Qclojure\E|\Qprolog\E|\Qscala\E){1}[\,]{0,1})+$/i', implode(",", $_POST["field-pl"]))) {
            $errors = TRUE;
        }
        if (empty($_POST["check-1"]) || $_POST["check-1"] != "accepted") {
            $errors = TRUE;
        }

        if ($errors) {
            header("Location: ./admin.php");
            exit();
        }

        $submission = array();
        $submission['name'] = strip_tags($_POST['field-name-1']);
        $submission['phone'] = strip_tags($_POST['field-tel']);
        $submission['email'] = strip_tags($_POST['field-email']);
        $submission['date'] = strip_tags($_POST['field-date']);
        $submission['gender'] = strip_tags($_POST['radio-group-2']);
        $submission['bio'] = strip_tags($_POST['field-bio']);
        $submission['fpls'] = array_map('strip_tags', $_POST['field-pl']);

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE application
            SET name = :name, phone = :phone, email = :email, bdate = :bdate, gender = :gender, bio = :bio
            WHERE user_id = :user_id");
            $stmt->bindParam('user_id', $_POST['user-id']);
            $stmt->bindParam('name', $submission['name']);
            $stmt->bindParam('phone', $submission['phone']);
            $stmt->bindParam('email', $submission['email']);
            $stmt->bindParam('bdate', $submission['date']);
            $gender = $submission["gender"] == "m" ? '1' : '0';
            $stmt->bindParam('gender', $gender);
            $stmt->bindParam('bio', $submission['bio']);
            $stmt->execute();

            $stmt = $db->prepare("SELECT id from application WHERE user_id = :user_id");
            $stmt->bindParam("user_id", $_POST['user-id']);
            $stmt->execute();
            $row_id = $stmt->fetchAll()[0]['id'];

            $stmt = $db->prepare("DELETE FROM fpls WHERE parent_id = :parent_id");
            $stmt->bindParam('parent_id', $row_id);
            $stmt->execute();

            foreach ($submission['fpls'] as $fpl) {
                $stmt = $db->prepare(sprintf("INSERT INTO fpls (parent_id, fpl) VALUES (%s, :fpl);", $row_id));
                $stmt->bindParam('fpl', $fpl);
                $stmt->execute();
            }

            $db->commit();
        }
        catch (PDOException $e) {
            $db->rollback();
            header("Location: ./admin.php");
            exit();
        }
        header("Location: ./admin.php");
    }
    else if ($_POST['button-action'] == "DELETE") {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT id from application WHERE user_id = :user_id");
            $stmt->bindParam("user_id", $_POST['user-id']);
            $stmt->execute();
            $row_id = $stmt->fetchAll()[0]['id'];

            $stmt = $db->prepare("DELETE FROM fpls WHERE parent_id = :parent_id");
            $stmt->bindParam('parent_id', $row_id);
            $stmt->execute();

            $stmt = $db->prepare("DELETE FROM application WHERE user_id = :user_id");
            $stmt->bindParam('user_id', $_POST['user-id']);
            $stmt->execute();

            $db->commit();
        }
        catch (PDOException $e) {
            $db->rollback();
            header("Location: ./admin.php");
            exit();
        }
        header("Location: ./admin.php");
    }
}
