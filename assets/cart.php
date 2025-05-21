<?php
session_start();

// Sepet boşsa oluştur
if (!isset($_SESSION['sepet'])) {
    $_SESSION['sepet'] = [];
}

// Ürün ekleme
if (isset($_POST['urun_id'])) {
    $urun_id = $_POST['urun_id'];
    
    // Ürün sepette var mı kontrol et
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]['miktar'] += 1;
    } else {
        // Veritabanından ürün bilgilerini çek (örnek olarak sabit değerler kullanıyorum)
        $urun = [
            'id' => $urun_id,
            'ad' => 'Ürün ' . $urun_id,
            'fiyat' => rand(50, 500),
            'miktar' => 1,
            'resim' => 'img/product' . $urun_id . '.jpg'
        ];
        $_SESSION['sepet'][$urun_id] = $urun;
    }
    
    echo json_encode(['success' => true, 'sepet_count' => count($_SESSION['sepet'])]);
    exit;
}

// Sepetten ürün silme
if (isset($_GET['sil'])) {
    $urun_id = $_GET['sil'];
    if (isset($_SESSION['sepet'][$urun_id])) {
        unset($_SESSION['sepet'][$urun_id]);
    }
    header('Location: cart.html');
    exit;
}

// Sepet içeriğini döndürme (AJAX için)
if (isset($_GET['get_cart'])) {
    echo json_encode($_SESSION['sepet']);
    exit;
}

// Sepeti boşaltma
if (isset($_GET['temizle'])) {
    $_SESSION['sepet'] = [];
    header('Location: cart.html');
    exit;
}

// Sepet içeriğini gösterme fonksiyonu
function sepetiGoster() {
    if (empty($_SESSION['sepet'])) {
        echo '<tr><td colspan="6">Sepetiniz boş</td></tr>';
        return;
    }
    
    $toplam = 0;
    foreach ($_SESSION['sepet'] as $urun) {
        $urunToplam = $urun['fiyat'] * $urun['miktar'];
        $toplam += $urunToplam;
        
        echo '<tr>
                <td><img src="'.$urun['resim'].'" width="50"></td>
                <td>'.$urun['ad'].'</td>
                <td>'.number_format($urun['fiyat'], 2).' TL</td>
                <td>
                    <input type="number" value="'.$urun['miktar'].'" min="1" class="miktar-input" data-id="'.$urun['id'].'">
                </td>
                <td>'.number_format($urunToplam, 2).' TL</td>
                <td><a href="?sil='.$urun['id'].'" class="sil-btn">Sil</a></td>
              </tr>';
    }
    
    echo '<script>document.getElementById("toplam").textContent = "'.number_format($toplam, 2).' TL";</script>';
}
?>