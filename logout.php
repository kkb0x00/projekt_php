<?php
include 'includes/header.php';

session_start();

if(isset($_SESSION['login'])) {
    session_regenerate_id();
    session_destroy();
}

redirect('index.php');

include 'includes/footer.php';