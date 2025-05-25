<?php
session_start();

// Sepet boşsa oluştur
if (!isset($_SESSION['sepet'])) {
    $_SESSION['sepet'] = [];
}

// AJAX isteği mi kontrolü
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Ürün ekleme
if (isset($_POST['urun_id'])) {
    $urun_id = $_POST['urun_id'];
    $urun_ad = isset($_POST['ad']) ? $_POST['ad'] : 'Ürün ' . $urun_id;
    $urun_fiyat = isset($_POST['fiyat']) ? floatval($_POST['fiyat']) : rand(50, 500);
    $urun_resim = isset($_POST['resim']) ? $_POST['resim'] : '';
    
    // Ürün sepette var mı kontrol et
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]['miktar'] += 1;
    } else {
        // Butondan gelen bilgileri kullan
        $urun = [
            'id' => $urun_id,
            'ad' => $urun_ad,
            'fiyat' => $urun_fiyat,
            'miktar' => 1,
            'resim' => $urun_resim
        ];
        $_SESSION['sepet'][$urun_id] = $urun;
    }
    
    if ($isAjax) {
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['sepet'])
        ]);
        exit;
    } else {
        // JSON yanıtı yerine cart.html'e yönlendir
        header('Location: cart.html');
        exit;
    }
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

// Ürün miktarını güncelleme
if (isset($_POST['urun_id']) && isset($_POST['miktar'])) {
    $urun_id = $_POST['urun_id'];
    $miktar = intval($_POST['miktar']);
    if (isset($_SESSION['sepet'][$urun_id])) {
        if ($miktar > 0) {
            $_SESSION['sepet'][$urun_id]['miktar'] = $miktar;
        } else {
            unset($_SESSION['sepet'][$urun_id]);
        }
    }
    if ($isAjax) {
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['sepet'])
        ]);
        exit;
    } else {
        header('Location: cart.html');
        exit;
    }
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