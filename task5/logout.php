<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (
        !empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])
    ) {
        session_destroy();
    }
    header("Location: ./");
    exit();
}
