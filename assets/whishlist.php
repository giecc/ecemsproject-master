<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

$serverName = "LAPTOP-069B9L8K\\SQLEXPRESS";
$connectionInfo = array(
    "Database" => "EWAHandmade",
    "UID" => "Bitirme_Projesi",
    "PWD" => "12345",
    "CharacterSet" => "UTF-8"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

$user_id = $_SESSION['user_id'];
$sql = "SELECT u.* FROM Favoriler f INNER JOIN Urunler u ON f.UrunID = u.UrunID WHERE f.KullaniciID = ?";
$params = array($user_id);
$stmt = sqlsrv_query($conn, $sql, $params);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favorilerim - EWA</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .wishlist-section {
            padding: 2rem 0;
        }
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        .wishlist-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.2s;
            position: relative;
        }
        .wishlist-card:hover {
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
        }
        .wishlist-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f7f7f7;
        }
        .wishlist-content {
            padding: 1rem 1.2rem 0.5rem 1.2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .wishlist-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #222;
        }
        .wishlist-price {
            color: #e67e22;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .wishlist-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }
        .wishlist-btn {
            flex: 1;
            padding: 0.5rem 0.7rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wishlist-btn.add-to-cart {
            background: #222;
            color: #fff;
        }
        .wishlist-btn.add-to-cart:hover {
            background: #e67e22;
            color: #fff;
        }
        .wishlist-btn.remove {
            background: #fff0f0;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        .wishlist-btn.remove:hover {
            background: #e74c3c;
            color: #fff;
        }
        .wishlist-empty {
            text-align: center;
            padding: 4rem 0;
            color: #aaa;
        }
        .wishlist-empty-icon {
            font-size: 3rem;
            color: #e67e22;
            margin-bottom: 1rem;
        }
        @media (max-width: 600px) {
            .wishlist-grid {
                grid-template-columns: 1fr;
            }
            .wishlist-img {
                height: 160px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <!-- Projenin header'ı -->
    </header>
    <main class="main">
        <section class="wishlist-section container">
            <h2 class="section__title">Favori Ürünlerim</h2>
            <?php
            $hasFav = false;
            if ($stmt) {
                $wishlist = array();
                while ($urun = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $hasFav = true;
                    $wishlist[] = $urun;
                }
            }
            if ($hasFav): ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist as $urun): ?>
                <div class="wishlist-card" data-id="<?php echo htmlspecialchars($urun['UrunID']); ?>">
                    <img src="<?php echo htmlspecialchars($urun['ResimURL']); ?>" alt="<?php echo htmlspecialchars($urun['UrunAdi']); ?>" class="wishlist-img">
                    <div class="wishlist-content">
                        <div class="wishlist-title"><?php echo htmlspecialchars($urun['UrunAdi']); ?></div>
                        <div class="wishlist-price">₺<?php echo number_format($urun['Fiyat'], 2); ?></div>
                        <div class="wishlist-actions">
                            <button class="wishlist-btn add-to-cart" onclick="addToCart('<?php echo htmlspecialchars($urun['UrunID']); ?>')">
                                <i class="fa-solid fa-cart-shopping" style="margin-right:6px;"></i> Sepete Ekle
                            </button>
                            <button class="wishlist-btn remove" onclick="removeFromFavorites('<?php echo htmlspecialchars($urun['UrunID']); ?>', this)">
                                <i class="fa-solid fa-trash" style="margin-right:6px;"></i> Kaldır
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="wishlist-empty">
                <div class="wishlist-empty-icon"><i class="fa-regular fa-heart"></i></div>
                <h3 class="wishlist-empty-title">Favori Ürününüz Bulunmuyor</h3>
            </div>
            <?php endif; ?>
        </section>
    </main>
    <footer class="footer">
        <!-- Projenin footer'ı -->
    </footer>
    <script>
    function addToCart(urunId) {
        // Sepete ekleme işlemi için AJAX yazabilirsin
        alert('Sepete eklendi: ' + urunId);
    }
    function removeFromFavorites(urunId, btn) {
        fetch('remove_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'urun_id=' + encodeURIComponent(urunId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Kartı animasyonla kaldır
                const card = btn.closest('.wishlist-card');
                card.style.transition = 'opacity 0.4s';
                card.style.opacity = 0;
                setTimeout(() => card.remove(), 400);
            }
            // Bildirim göster
            alert(data.message);
        });
    }
    </script>
</body>
</html> 