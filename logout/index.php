<?php
include_once('../session.php');
session_start();
session_unset();
session_destroy();
session_start();
goBack();
