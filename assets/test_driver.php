<?php
if (extension_loaded('sqlsrv')) {
    echo "SQLSRV driver yüklendi.";
} else {
    echo "SQLSRV driver yüklü değil.";
}

if (extension_loaded('pdo_sqlsrv')) {
    echo "PDO_SQLSRV driver yüklendi.";
} else {
    echo "PDO_SQLSRV driver yüklü değil.";
}
?>
