<?php
require_once 'Urun.php';
session_start();

$urun = new Urun();
$urunler = $urun->tumUrunleriGetir();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ürün Listesi</title>
</head>
<body>
    <h1>Ürünler</h1>

    <?php foreach ($urunler as $u): ?>
        <div style="border:1px solid #ccc; margin:10px; padding:10px;">
            <h3><?= htmlspecialchars($u['UrunAdi']) ?></h3>
            <p><?= htmlspecialchars($u['Aciklama']) ?></p>
            <p>Kategori: <?= htmlspecialchars($u['Kategori']) ?></p>
            <p>Fiyat: <?= htmlspecialchars($u['Fiyat']) ?> TL</p>
            <img src="<?= htmlspecialchars($u['ResimURL']) ?>" width="150" alt="Resim Yok">
            <form method="POST" action="sepet.php">
                <input type="hidden" name="urun_id" value="<?= $u['UrunID'] ?>">
                <button type="submit">Sepete Ekle</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
