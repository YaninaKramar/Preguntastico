<?php
session_start();
session_unset();
session_destroy();
header("Location: /Preguntastico/TP-FinalPW2/index.php");
exit;