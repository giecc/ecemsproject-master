<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['urun_id'], $_POST['miktar'])) {
    $urun_id = $_POST['urun_id'];
    $miktar = (int)$_POST['miktar'];
    
    if (isset($_SESSION['sepet'][$urun_id]) && $miktar > 0) {
        $_SESSION['sepet'][$urun_id]['miktar'] = $miktar;
    }
}

header('Location: sepet.php');
exit;
?>