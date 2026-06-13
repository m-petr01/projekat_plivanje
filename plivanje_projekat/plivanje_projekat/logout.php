<?php

require_once __DIR__ . '/classes/Session.php';

Session::logout();

header('Location: login.php');
exit;