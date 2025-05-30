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
        <button class="cart__btn add-to-cart"
                data-id="<?= $urun['UrunID'] ?>"
                data-name="<?= htmlspecialchars($urun['UrunAdi']) ?>"
                data-price="<?= number_format($urun['Fiyat'], 2) ?>"
                data-image="<?= htmlspecialchars($urun['ResimURL']) ?>">
            <i class="fa-solid fa-cart-shopping"></i>
            Sepete Ekle
        </button>
    </div>
    <?php endforeach; ?>
</div>