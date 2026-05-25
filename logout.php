<?php
require_once __DIR__ . '/config.php';

session_destroy();
session_start();
flash('Успешно излязохте от системата.');
redirect('login.php');
