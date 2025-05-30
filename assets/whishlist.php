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
$sql = "SELECT f.FavoriID, u.* FROM Favoriler f INNER JOIN Urunler u ON f.UrunID = u.UrunID WHERE f.KullaniciID = ?";
$params = array($user_id);
$stmt = sqlsrv_query($conn, $sql, $params);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favorilerim - EWA</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .wishlist-list {
            max-width: 700px;
            margin: 2rem auto;
            padding: 0;
            list-style: none;
        }
        .wishlist-row {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            margin-bottom: 1.2rem;
            padding: 1rem 1.5rem;
            transition: box-shadow 0.2s;
        }
        .wishlist-row:hover {
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
        }
        .wishlist-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            background: #f7f7f7;
            margin-right: 1.5rem;
        }
        .wishlist-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }
        .wishlist-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #222;
        }
        .wishlist-price {
            color: #e67e22;
            font-size: 1.05rem;
            font-weight: 500;
        }
        .wishlist-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: 1.5rem;
        }
        .wishlist-btn {
            padding: 0.45rem 0.9rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .wishlist-btn.add-to-cart {
            background: var(--first-color);
            color: var(--container-color);
        }
        .wishlist-btn.add-to-cart:hover {
            background: var(--first-color-alt);
            color: var(--title-color);
        }
        .wishlist-btn.remove {
            background: #222;
            color: #fff;
            border: none;
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
            .wishlist-row {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
            }
            .wishlist-img {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            .wishlist-actions {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
<header class="header">
        <div class="header__top">
            <div class="header__container container">
                <div class="header__contact">
                    <span><i class="fas fa-phone"></i> 0533 773 25 05</span>
                    <span><i class="fas fa-map-marker-alt"></i> Fethiye / MUĞLA</span>
                </div>
                <a href="login-register.html" class="header__top-action">
                    Giriş yap / Üye Ol
                </a>
            </div>
        </div>

        <nav class="nav container">
            <a href="index.html" class="nav__logo">
                <img src="img/logo.png" alt="EWA Logo" class="nav__logo-img">
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="index.html" class="nav__link active-link">Ana Sayfa</a>
                    </li>
                    <li class="nav__item">
                        <a href="shop.html" class="nav__link">Mağaza</a>
                        <div class="dropdown-menu" id="shop-dropdown">
                            <a href="tunik.html">Tunik</a>
                            <a href="elbise.html">Elbise</a>
                            <a href="otontikY.html">Otantik Uzun Yelek</a>
                            <a href="bolero.html">Bolero</a>
                            <a href="yorselD.html">Yöresel El Dokuması</a>
                            <a href="panco.html">Panço</a>
                            <a href="sal.html">Şal</a>
                            <a href="fular.html">Fular</a>
                            <a href="pestemal.html">Peştemal</a>
                            <a href="diger.html">Diğer</a>
                        </div>
                    </li>
                    <li class="nav__item">
                        <a href="accounts.php" class="nav__link">Hesabım</a>
                    </li>
                    <li class="nav__item">
                        <a href="contact.html" class="nav__link">İletişim</a>
                    </li>
                    <li class="nav__item">
                        <a href="login-register.html" class="nav__link">Giriş Yap</a>
                    </li>
                </ul>

                <div class="header__search">
                    <input type="text" placeholder="Ürün ara..." class="form__input" />
                    <button class="search__btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            <div class="header__user-actions">
                <a href="whishlist.php" class="header__action-btn">
                    <i class="fa-regular fa-heart"></i>
                    <span class="count">3</span>
                </a>
                <a href="cart.html" class="header__action-btn">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="count">3</span>
                </a>
            </div>
        </nav>
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
            <ul class="wishlist-list">
                <?php foreach ($wishlist as $urun): ?>
                <li class="wishlist-row" data-id="<?php echo htmlspecialchars($urun['UrunID']); ?>" data-favorite-id="<?php echo htmlspecialchars($urun['FavoriID']); ?>">
                    <img src="<?php echo htmlspecialchars($urun['ResimURL']); ?>" alt="<?php echo htmlspecialchars($urun['UrunAdi']); ?>" class="wishlist-img">
                    <div class="wishlist-info">
                        <div class="wishlist-title"><?php echo htmlspecialchars($urun['UrunAdi']); ?></div>
                        <div class="wishlist-price">₺<?php echo number_format($urun['Fiyat'], 2); ?></div>
                    </div>
                    <div class="wishlist-actions">
                        <button class="wishlist-btn add-to-cart" onclick="addToCart('<?php echo htmlspecialchars($urun['UrunID']); ?>')">
                            <i class="fa-solid fa-cart-shopping"></i> Sepete Ekle
                        </button>
                        <button class="wishlist-btn remove" onclick="removeFromFavorites('<?php echo htmlspecialchars($urun['FavoriID']); ?>', this)">
                            <i class="fa-solid fa-trash"></i> Kaldır
                        </button>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="wishlist-empty">
                <div class="wishlist-empty-icon"><i class="fa-regular fa-heart"></i></div>
                <h3 class="wishlist-empty-title">Favori Ürününüz Bulunmuyor</h3>
            </div>
            <?php endif; ?>
        </section>
    </main>
    <footer class="footer">
        <div class="footer__container container grid">
            <div class="footer__content">
                <a href="index.html" class="footer__logo">
                    <img src="img/logo.png" alt="EWA Logo" class="footer__logo-img">
                </a>
                <h4 class="footer__subtitle">İletişim</h4>
                <p class="footer__description">
                    <span>Adres:</span> Cumhuriyet Mah. 41. Sok. No:8A Fethiye / MUĞLA / TÜRKİYE
                </p>
                <p class="footer__description">
                    0533 773 25 05
                </p>
                <p class="footer__description">
                    emel@ewahandmade.com
                </p>
                <div class="footer__social">
                    <div class="footer__social-links flex">
                        <a href="#">
                            <img src="img/facebook-icon.svg" alt="Facebook" class="footer__social-icon">
                        </a>
                        <a href="#">
                            <img src="img/instagram-icon.svg" alt="Instagram" class="footer__social-icon">
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer__content">
                <h3 class="footer__title">Biz</h3>
                <ul class="footer__links">
                    <li>Emel's Weaving Art olarak, %100</li>
                    <li>pamuk iplikleriyle dokuduğumuz</li>
                    <li>kumaşlara ruh katarız.</li>
                </ul>
            </div>

            <div class="footer__content">
                <h3 class="footer__title">Biz Ne Yapıyoruz</h3>
                <ul class="footer__links">
                    <li>Emel's Weaving Art olarak,</li>
                    <li>hayalimizdeki tasarımlara uygun</li>
                    <li>dokumalar yaparak başlarız işe.</li>
                    <li>%100 pamuk iplikleri kullanarak.</li>
                </ul>
            </div>

            <div class="footer__content">
                <h3 class="footer__title">Ürünlerimiz</h3>
                <ul class="footer__links">
                    <li><a href="#" class="footer__link">Elbise</a></li>
                    <li><a href="#" class="footer__link">Tunik</a></li>
                    <li><a href="#" class="footer__link">Otantik Yelek</a></li>
                    <li><a href="#" class="footer__link">Bolero</a></li>
                    <li><a href="#" class="footer__link">Panço</a></li>
                    <li><a href="#" class="footer__link">Şal/Fular</a></li>
                    <li><a href="#" class="footer__link">Peştemal</a></li>
                    <li><a href="#" class="footer__link">Yöresel Dokuma</a></li>
                </ul>
            </div>
        </div>
    </footer>
    <script>
    function addToCart(urunId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'urun_id=' + encodeURIComponent(urunId)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                // Başarılı ekleme durumunda sepet sayısını güncelle
                const cartCount = document.querySelector('.header__action-btn:nth-child(2) .count');
                if (cartCount) {
                    const currentCount = parseInt(cartCount.textContent) || 0;
                    cartCount.textContent = currentCount + 1;
                }
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        });
    }
    function removeFromFavorites(favoriteId, btn) {
        fetch('remove_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'favorite_id=' + encodeURIComponent(favoriteId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Satırı animasyonla kaldır
                const row = btn.closest('.wishlist-row');
                row.style.transition = 'opacity 0.4s';
                row.style.opacity = 0;
                setTimeout(() => row.remove(), 400);
                
                // Favori sayısını güncelle
                const favCount = document.querySelector('.header__action-btn:nth-child(1) .count');
                if (favCount) {
                    const currentCount = parseInt(favCount.textContent) || 0;
                    favCount.textContent = Math.max(0, currentCount - 1);
                }
            }
            alert(data.message);
        });
    }
    </script>
</body>
</html> 