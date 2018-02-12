<?php

session_start();
require('../config.php');
require('../init.php');

if (isset($_POST['act']) && $_POST['act'] === 'login' && !empty($_POST['login']) && !empty($_POST['pass']) && isset($_POST['id']) && $_POST['id'] === '') {
	Manage::logIn($_POST['login'], $_POST['pass']);
} else if (isset($_GET['act']) && $_GET['act'] === 'logout') {
	Manage::logOut();
}

if (!isset($_SESSION['secret'])) {
	header('Location: /' . ADMINDIR . '/login.php');
	exit;
}
define('AUTHORIZED', 1);