<?php

function connect_to_db()
{
    try {
        include ("./db_data.php");
        $db = new PDO('mysql:host=localhost;dbname=u67441', $user, $pass, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $db;
    } catch (PDOException $e) {
        exit();
    }
}

function get_user_id($db, $login, $password_hash) {
    try {
        $stmt = $db->prepare('SELECT user_id FROM users WHERE
        login = :login AND password_hash = :password_hash');
        $stmt->bindParam('login', $login);
        $stmt->bindParam('password_hash', $password_hash);
        $stmt->execute();

        $result = $stmt->fetchAll();
        if (!$result || count($result) == 0) {
            $user_id = -1;
        } else {
            $user_id = $result[0]['user_id'];
        }
    } catch (Exception $e) {
        $user_id = -1;
    }

    return $user_id;
}

function get_user_submission($db, $user_id) {
    try {
        $stmt = $db->prepare('SELECT * FROM application WHERE
        user_id = :user_id');
        $stmt->bindParam('user_id', $user_id);
        $stmt->execute();

        $submission = $stmt->fetchAll();

        if (!empty($submission)) {
            $submission_id = $submission[0]["id"];

            $stmt = $db->prepare('SELECT fpl FROM fpls WHERE
            parent_id = :parent_id');
            $stmt->bindParam('parent_id', $submission_id);
            $stmt->execute();

            $fpls = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $submission[0]['fpls'] = $fpls;
        }

    } catch (Exception $e) {
        $submission = array();
    }
    return $submission;
}

function get_admin_db_data($db, $login, $password_hash)
{
    try {
        $stmt = $db->prepare('SELECT id FROM admins WHERE
        login = :login AND password_hash = :password_hash');
        $stmt->bindParam('login', $login);
        $stmt->bindParam('password_hash', $password_hash);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Exception $e) {
        header("Location: ./admin.php");
        exit();
    }
}

function get_user_fpls($db, $submission_id)
{
    try {
        $stmt = $db->prepare('SELECT fpl FROM fpls WHERE
        parent_id = :parent_id');
        $stmt->bindParam('parent_id', $submission_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        header("Location: ./");
        exit();
    }
}

function get_form_submissions($db)
{
    try {
        $stmt = $db->prepare('SELECT * FROM application');
        $stmt->execute();

        $submissions = $stmt->fetchAll();

        foreach ($submissions as &$submission) {
            $stmt = $db->prepare('SELECT fpl FROM fpls WHERE parent_id = :parent_id');
            $stmt->bindParam('parent_id', $submission['id']);
            $stmt->execute();
            $submission['fpls'] = array();
            $fpls = $stmt->fetchAll();
            foreach ($fpls as &$fpl) {
                array_push($submission['fpls'], $fpl['fpl']);
            }
            unset($fpl);
        }
        unset($submission);

        return $submissions;
    } catch (Exception $e) {
        header("Location: ./");
        exit();
    }
}

function count_fpls($submissions) {
    $fpls_count = array();
    foreach ($submissions as $submission) {
        foreach ($submission['fpls'] as $fpl) {
            if (array_key_exists($fpl, $fpls_count)) {
                $fpls_count[$fpl] += 1;
            }
            else {
                $fpls_count[$fpl] = 1;
            }
        }
    }
    return $fpls_count;
}
