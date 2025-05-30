<?php
session_start();
require_once 'Urun.php';

// Sepetten ürün silme
if (isset($_GET['sil'])) {
    $urun_id = $_GET['sil'];
    if (isset($_SESSION['sepet'][$urun_id])) {
        unset($_SESSION['sepet'][$urun_id]);
    }
    header('Location: sepet.php');
    exit;
}

// Sepeti boşaltma
if (isset($_GET['temizle'])) {
    $_SESSION['sepet'] = [];
    header('Location: sepet.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sepetim</title>
</head>
<body>
    <h1>Sepetiniz</h1>
    
    <?php if (empty($_SESSION['sepet'])): ?>
        <p>Sepetiniz boş</p>
    <?php else: ?>
        <table border="1" width="100%">
            <tr>
                <th>Ürün</th>
                <th>Ad</th>
                <th>Fiyat</th>
                <th>Miktar</th>
                <th>Toplam</th>
                <th>İşlem</th>
            </tr>
            
            <?php 
            $genel_toplam = 0;
            foreach ($_SESSION['sepet'] as $urun): 
                $urun_toplam = $urun['fiyat'] * $urun['miktar'];
                $genel_toplam += $urun_toplam;
            ?>
                <tr>
                    <td><img src="<?= $urun['resim'] ?>" width="50"></td>
                    <td><?= htmlspecialchars($urun['ad']) ?></td>
                    <td><?= number_format($urun['fiyat'], 2) ?> TL</td>
                    <td>
                        <form method="post" action="sepet_guncelle.php">
                            <input type="hidden" name="urun_id" value="<?= $urun['id'] ?>">
                            <input type="number" name="miktar" value="<?= $urun['miktar'] ?>" min="1">
                            <button type="submit">Güncelle</button>
                        </form>
                    </td>
                    <td><?= number_format($urun_toplam, 2) ?> TL</td>
                    <td><a href="sepet.php?sil=<?= $urun['id'] ?>">Sil</a></td>
                </tr>
            <?php endforeach; ?>
            
            <tr>
                <td colspan="4" align="right"><strong>Genel Toplam:</strong></td>
                <td><?= number_format($genel_toplam, 2) ?> TL</td>
                <td><a href="sepet.php?temizle=1">Sepeti Boşalt</a></td>
            </tr>
        </table>
        
        <a href="odeme.php">Ödemeye Geç</a>
    <?php endif; ?>
    
    <a href="urunler.php">Alışverişe Devam Et</a>
</body>
</html>