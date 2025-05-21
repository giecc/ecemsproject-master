<?php
session_start();
require_once 'models/Urun.php';

$urunModel = new Urun();
$urunler = $urunModel->tumUrunleriGetir();
?>

<div class="urun-listesi">
    <?php foreach ($urunler as $urun): ?>
    <div class="urun">
        <img src="<?= htmlspecialchars($urun['ResimURL']) ?>" alt="<?= htmlspecialchars($urun['UrunAdi']) ?>">
        <h3><?= htmlspecialchars($urun['UrunAdi']) ?></h3>
        <p><?= number_format($urun['Fiyat'], 2) ?> TL</p>
        <a href="#" class="action__btn cart__btn" aria-label="Add To Cart" data-id="<?= $urun['UrunID'] ?>">
            <i class="fi fi-rs-shopping-bag-add"></i>
        </a>
    </div>
    <?php endforeach; ?>
</div>