<?php
session_start();
require_once 'messages.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login-register.html");
    exit();
}

// Kullanıcı bilgilerini veritabanından al
$user_id = $_SESSION['user_id'];
$serverName = "LAPTOP-069B9L8K\\SQLEXPRESS";
$connectionInfo = array(
    "Database" => "EWAHandmade",
    "UID" => "Bitirme_Projesi",
    "PWD" => "12345",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    $errors = sqlsrv_errors();
    die("Veritabanı bağlantı hatası: " . $errors[0]['message']);
}

$sql = "SELECT * FROM Kullanicilar WHERE KullaniciID = ?";
$params = array($user_id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $errors = sqlsrv_errors();
    die("Sorgu hatası: " . $errors[0]['message']);
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login-register.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <title>EWA - Hesabım</title>
</head>

<body>
    <!--============header===========-->
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

    <!--============main===========-->
    <main class="main">
        <div class="container">
            <div class="account-section">
                <div class="account-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($_SESSION['user_name'] . ' ' . $_SESSION['user_surname']); ?></h3>
                        <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    </div>
                    <nav class="account-nav">
                        <a href="#profile" class="active"><i class="fas fa-user"></i> Profil Bilgileri</a>
                        <a href="#orders"><i class="fas fa-shopping-bag"></i> Siparişlerim</a>
                        <a href="#favorites"><i class="fas fa-heart"></i> Favorilerim</a>
                        <a href="#addresses"><i class="fas fa-map-marker-alt"></i> Adreslerim</a>
                        <a href="#password"><i class="fas fa-lock"></i> Şifre Değiştir</a>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
                    </nav>
                </div>

                <div class="account-content">
                    <!-- Profil Bilgileri -->
                    <section id="profile" class="account-tab active">
                        <h2>Profil Bilgileri</h2>
                        <?php displayMessages(); ?>
                        <form class="account-form" method="POST" action="update_profile.php">
                            <div class="form-group">
                                <label>Ad</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Soyad</label>
                                <input type="text" name="surname" value="<?php echo htmlspecialchars($_SESSION['user_surname']); ?>">
                            </div>
                            <div class="form-group">
                                <label>E-posta</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
                            </div>
                            <button type="submit" class="btn btn--primary">Bilgileri Güncelle</button>
                        </form>
                    </section>

                    <!-- Siparişlerim -->
                    <section id="orders" class="account-tab">
                        <h2>Siparişlerim</h2>
                        <?php
                        // Siparişleri veritabanından çek
                        $sql = "SELECT s.*, sd.*, u.UrunAdi, u.ResimURL 
                               FROM Siparisler s 
                               INNER JOIN SiparisDetaylari sd ON s.SiparisID = sd.SiparisID
                               INNER JOIN Urunler u ON sd.UrunID = u.UrunID
                               WHERE s.KullaniciID = ? 
                               ORDER BY s.SiparisTarihi DESC";
                        $params = array($_SESSION['user_id']);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        ?>
                        <style>
                        .orders-list {
                            max-width: 800px;
                            margin: 2rem auto;
                        }
                        .order-item {
                            background: #fff;
                            border-radius: 10px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                            margin-bottom: 1.5rem;
                            overflow: hidden;
                        }
                        .order-header {
                            background: #f8f9fa;
                            padding: 1rem 1.5rem;
                            border-bottom: 1px solid #eee;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            flex-wrap: wrap;
                            gap: 1rem;
                        }
                        .order-number {
                            font-weight: 600;
                            color: #222;
                        }
                        .order-date {
                            color: #666;
                        }
                        .order-status {
                            padding: 0.3rem 0.8rem;
                            border-radius: 20px;
                            font-size: 0.9rem;
                            font-weight: 500;
                        }
                        .order-status.pending {
                            background: #fff3cd;
                            color: #856404;
                        }
                        .order-status.processing {
                            background: #cce5ff;
                            color: #004085;
                        }
                        .order-status.shipped {
                            background: #d4edda;
                            color: #155724;
                        }
                        .order-status.delivered {
                            background: #d1e7dd;
                            color: #0f5132;
                        }
                        .order-status.cancelled {
                            background: #f8d7da;
                            color: #721c24;
                        }
                        .order-content {
                            padding: 1.5rem;
                        }
                        .order-section {
                            margin-bottom: 1.5rem;
                        }
                        .order-section:last-child {
                            margin-bottom: 0;
                        }
                        .order-section h4 {
                            color: #222;
                            margin-bottom: 0.8rem;
                            font-size: 1.1rem;
                        }
                        .order-info {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                            gap: 1rem;
                        }
                        .info-item {
                            margin-bottom: 0.5rem;
                        }
                        .info-label {
                            color: #666;
                            font-size: 0.9rem;
                            margin-bottom: 0.2rem;
                        }
                        .info-value {
                            color: #222;
                            font-weight: 500;
                        }
                        .order-products {
                            margin-top: 1rem;
                        }
                        .product-item {
                            display: flex;
                            align-items: center;
                            padding: 1rem;
                            border: 1px solid #eee;
                            border-radius: 8px;
                            margin-bottom: 0.8rem;
                        }
                        .product-item:last-child {
                            margin-bottom: 0;
                        }
                        .product-img {
                            width: 80px;
                            height: 80px;
                            object-fit: cover;
                            border-radius: 6px;
                            margin-right: 1rem;
                        }
                        .product-info {
                            flex: 1;
                        }
                        .product-name {
                            font-weight: 500;
                            color: #222;
                            margin-bottom: 0.3rem;
                        }
                        .product-details {
                            color: #666;
                            font-size: 0.9rem;
                        }
                        .order-total {
                            text-align: right;
                            padding: 1rem 1.5rem;
                            background: #f8f9fa;
                            border-top: 1px solid #eee;
                        }
                        .total-amount {
                            font-size: 1.2rem;
                            font-weight: 600;
                            color: #e67e22;
                        }
                        @media (max-width: 768px) {
                            .order-header {
                                flex-direction: column;
                                align-items: flex-start;
                            }
                            .order-info {
                                grid-template-columns: 1fr;
                            }
                            .product-item {
                                flex-direction: column;
                                text-align: center;
                            }
                            .product-img {
                                margin-right: 0;
                                margin-bottom: 1rem;
                            }
                        }
                        </style>
                        <div class="orders-list">
                            <?php
                            if ($stmt) {
                                $currentOrderId = null;
                                $orderItems = array();
                                
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    if ($currentOrderId !== $row['SiparisID']) {
                                        // Eğer önceki sipariş varsa, onu göster
                                        if ($currentOrderId !== null) {
                                            ?>
                                            <div class="order-item">
                                                <div class="order-header">
                                                    <span class="order-number">Sipariş No: <?php echo htmlspecialchars($orderItems[0]['SiparisNo']); ?></span>
                                                    <span class="order-date"><?php echo date_format($orderItems[0]['SiparisTarihi'], 'd.m.Y H:i'); ?></span>
                                                    <span class="order-status <?php echo strtolower($orderItems[0]['Durum']); ?>">
                                                        <?php echo htmlspecialchars($orderItems[0]['Durum']); ?>
                                                    </span>
                                                </div>
                                                <div class="order-content">
                                                    <div class="order-section">
                                                        <h4>Sipariş Edilen Ürünler</h4>
                                                        <div class="order-products">
                                                            <?php foreach ($orderItems as $item): ?>
                                                            <div class="product-item">
                                                                <img src="<?php echo htmlspecialchars($item['ResimURL']); ?>" 
                                                                     alt="<?php echo htmlspecialchars($item['UrunAdi']); ?>" 
                                                                     class="product-img">
                                                                <div class="product-info">
                                                                    <div class="product-name"><?php echo htmlspecialchars($item['UrunAdi']); ?></div>
                                                                    <div class="product-details">
                                                                        <div>Adet: <?php echo $item['Adet']; ?></div>
                                                                        <div>Birim Fiyat: <?php echo number_format($item['BirimFiyat'], 2); ?> TL</div>
                                                                        <div>Toplam: <?php echo number_format($item['Adet'] * $item['BirimFiyat'], 2); ?> TL</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="order-total">
                                                    <span class="total-amount">Toplam Tutar: <?php echo number_format($orderItems[0]['ToplamTutar'], 2); ?> TL</span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        
                                        // Yeni sipariş için değişkenleri sıfırla
                                        $currentOrderId = $row['SiparisID'];
                                        $orderItems = array();
                                    }
                                    
                                    // Mevcut ürünü sipariş listesine ekle
                                    $orderItems[] = $row;
                                }
                                
                                // Son siparişi göster
                                if ($currentOrderId !== null) {
                                    ?>
                                    <div class="order-item">
                                        <div class="order-header">
                                            <span class="order-number">Sipariş No: <?php echo htmlspecialchars($orderItems[0]['SiparisNo']); ?></span>
                                            <span class="order-date"><?php echo date_format($orderItems[0]['SiparisTarihi'], 'd.m.Y H:i'); ?></span>
                                            <span class="order-status <?php echo strtolower($orderItems[0]['Durum']); ?>">
                                                <?php echo htmlspecialchars($orderItems[0]['Durum']); ?>
                                            </span>
                                        </div>
                                        <div class="order-content">
                                            <div class="order-section">
                                                <h4>Sipariş Edilen Ürünler</h4>
                                                <div class="order-products">
                                                    <?php foreach ($orderItems as $item): ?>
                                                    <div class="product-item">
                                                        <img src="<?php echo htmlspecialchars($item['ResimURL']); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['UrunAdi']); ?>" 
                                                             class="product-img">
                                                        <div class="product-info">
                                                            <div class="product-name"><?php echo htmlspecialchars($item['UrunAdi']); ?></div>
                                                            <div class="product-details">
                                                                <div>Adet: <?php echo $item['Adet']; ?></div>
                                                                <div>Birim Fiyat: <?php echo number_format($item['BirimFiyat'], 2); ?> TL</div>
                                                                <div>Toplam: <?php echo number_format($item['Adet'] * $item['BirimFiyat'], 2); ?> TL</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order-total">
                                            <span class="total-amount">Toplam Tutar: <?php echo number_format($orderItems[0]['ToplamTutar'], 2); ?> TL</span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="no-orders">Henüz siparişiniz bulunmamaktadır.</div>';
                            }
                            ?>
                        </div>
                    </section>

                    <!-- Favorilerim -->
                    <section id="favorites" class="account-tab">
                        <h2>Favorilerim</h2>
                        <?php
                        // Favorileri veritabanından çek
                        $sql = "SELECT f.FavoriID, u.* FROM Favoriler f 
                               INNER JOIN Urunler u ON f.UrunID = u.UrunID 
                               WHERE f.KullaniciID = ?";
                        $params = array($_SESSION['user_id']);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        ?>
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

                    <!-- Adreslerim -->
                    <section id="addresses" class="account-tab">
                        <h2>Adreslerim</h2>
                        <?php
                        // Adresleri veritabanından çek
                        $sql = "SELECT * FROM Adresler WHERE KullaniciID = ?";
                        $params = array($_SESSION['user_id']);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        ?>
                        <style>
                        .addresses-list {
                            max-width: 700px;
                            margin: 2rem auto 1.5rem auto;
                            padding: 0;
                            list-style: none;
                        }
                        .address-row {
                            background: #fff;
                            border-radius: 10px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                            margin-bottom: 1.2rem;
                            padding: 1.2rem 1.5rem;
                            display: flex;
                            flex-direction: column;
                            gap: 0.5rem;
                            position: relative;
                        }
                        .address-title {
                            font-size: 1.1rem;
                            font-weight: 600;
                            color: #222;
                        }
                        .address-detail {
                            color: #444;
                            font-size: 1rem;
                        }
                        .address-actions {
                            display: flex;
                            gap: 0.5rem;
                            margin-top: 0.5rem;
                        }
                        .address-btn {
                            padding: 0.4rem 0.9rem;
                            border: none;
                            border-radius: 6px;
                            font-size: 1rem;
                            cursor: pointer;
                            transition: background 0.2s, color 0.2s;
                        }
                        .address-btn.edit {
                            background: var(--first-color);
                            color: var(--container-color);
                        }
                        .address-btn.edit:hover {
                            background: var(--first-color-alt);
                            color: var(--title-color);
                        }
                        .address-btn.delete {
                            background: #222;
                            color: #fff;
                        }
                        .address-btn.delete:hover {
                            background: #e74c3c;
                            color: #fff;
                        }
                        .address-empty {
                            text-align: center;
                            padding: 3rem 0;
                            color: #aaa;
                        }
                        .address-form {
                            background: #fff;
                            border-radius: 10px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                            max-width: 500px;
                            margin: 2rem auto;
                            padding: 2rem 1.5rem 1.5rem 1.5rem;
                        }
                        .address-form h3 {
                            margin-bottom: 1rem;
                        }
                        .address-form .form-group {
                            margin-bottom: 1rem;
                        }
                        .address-form label {
                            display: block;
                            margin-bottom: 0.3rem;
                            color: #222;
                        }
                        .address-form input,
                        .address-form textarea {
                            width: 100%;
                            padding: 0.5rem;
                            border-radius: 6px;
                            border: 1px solid #ddd;
                            font-size: 1rem;
                        }
                        .address-form textarea {
                            min-height: 60px;
                            resize: vertical;
                        }
                        .address-form .form-actions {
                            display: flex;
                            gap: 0.5rem;
                            margin-top: 1rem;
                        }
                        @media (max-width: 600px) {
                            .addresses-list, .address-form {
                                max-width: 100%;
                                padding: 1rem;
                            }
                        }
                        </style>
                        <?php
                        $hasAddress = false;
                        if ($stmt) {
                            $addresses = array();
                            while ($address = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                $hasAddress = true;
                                $addresses[] = $address;
                            }
                        }
                        if ($hasAddress): ?>
                        <ul class="addresses-list">
                            <?php foreach ($addresses as $address): ?>
                            <li class="address-row" data-id="<?php echo htmlspecialchars($address['AdresID']); ?>">
                                <div class="address-title"><?php echo htmlspecialchars($address['AdresBasligi']); ?></div>
                                <div class="address-detail"><?php echo htmlspecialchars($address['Adres']); ?></div>
                                <div class="address-detail"><?php echo htmlspecialchars($address['Ilce'] . ' / ' . $address['Sehir']); ?></div>
                                <div class="address-detail">Posta Kodu: <?php echo htmlspecialchars($address['PostaKodu']); ?></div>
                                <div class="address-actions">
                                    <button class="address-btn edit" onclick="editAddress(<?php echo $address['AdresID']; ?>)"><i class="fa-solid fa-pen"></i> Düzenle</button>
                                    <button class="address-btn delete" onclick="deleteAddress(<?php echo $address['AdresID']; ?>)"><i class="fa-solid fa-trash"></i> Sil</button>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <div class="address-empty">
                            <i class="fa-solid fa-map-location-dot" style="font-size:2.5rem;color:var(--first-color);"></i>
                            <h3>Henüz adres eklemediniz.</h3>
                        </div>
                        <?php endif; ?>
                        <button class="address-btn edit" style="margin:1rem auto;display:block;" onclick="showAddressForm()"><i class="fa-solid fa-plus"></i> Yeni Adres Ekle</button>
                        <!-- Yeni Adres Ekleme Formu -->
                        <div id="addressForm" style="display: none;" class="address-form">
                            <h3 id="addressFormTitle">Yeni Adres Ekle</h3>
                            <form id="addressFormElement" action="add_address.php" method="POST">
                                <input type="hidden" name="address_id" id="address_id" value="">
                                <div class="form-group">
                                    <label>Ad</label>
                                    <input type="text" name="ad" id="ad" required>
                                </div>
                                <div class="form-group">
                                    <label>Soyad</label>
                                    <input type="text" name="soyad" id="soyad" required>
                                </div>
                                <div class="form-group">
                                    <label>Telefon</label>
                                    <input type="tel" name="telefon" id="telefon" required pattern="0[0-9]{10}">
                                </div>
                                <div class="form-group">
                                    <label>İl</label>
                                    <select id="il" name="il" required>
                                        <option value="">İl seçiniz</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>İlçe</label>
                                    <select id="ilce" name="ilce" required>
                                        <option value="">İlçe seçiniz</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Mahalle</label>
                                    <input type="text" name="mahalle" id="mahalle" required>
                                </div>
                                <div class="form-group">
                                    <label>Adres</label>
                                    <textarea name="address" id="address" required placeholder="Cadde, sokak, bina, daire vb."></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Adres Başlığı</label>
                                    <input type="text" name="address_title" id="address_title" required placeholder="Örn: Ev, İş, Yurt">
                                </div>
                                <div class="form-group">
                                    <label>Fatura Türü</label>
                                    <label><input type="radio" name="fatura_turu" value="Bireysel" checked> Bireysel</label>
                                    <label style="margin-left:10px;"><input type="radio" name="fatura_turu" value="Kurumsal"> Kurumsal</label>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="address-btn edit" id="addressFormSubmit">Kaydet</button>
                                    <button type="button" class="address-btn delete" onclick="hideAddressForm()">İptal</button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Şifre Değiştir -->
                    <!-- Şifre değiştir bölümü kaldırıldı -->
                </div>
            </div>
        </div>
    </main>

    <!--============footer==========-->
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

    <!--============swiper js===========-->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!--============main js===========-->
    <script src="js/main.js"></script>


    <script>
        // Tab switching functionality
        document.querySelectorAll('.account-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and tabs
                document.querySelectorAll('.account-nav a').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.account-tab').forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Show corresponding tab
                const tabId = this.getAttribute('href').substring(1);
                document.getElementById(tabId).classList.add('active');
            });
        });

        function showAddressForm(address = null) {
    document.getElementById('addressForm').style.display = 'block';
    document.getElementById('addressFormTitle').innerText = address ? 'Adresi Düzenle' : 'Yeni Adres Ekle';
    document.getElementById('addressFormElement').action = address ? 'edit_address.php' : 'add_address.php';
    document.getElementById('address_id').value = address ? address.AdresID : '';
    document.getElementById('ad').value = address ? address.AdresBasligi : '';
    document.getElementById('address').value = address ? address.Adres : '';
    document.getElementById('mahalle').value = address ? address.Mahalle : '';

    // İl ve ilçe select'lerini doldur
    if (address) {
        fillIlSelect(address.Sehir);
        fillIlceSelect(address.Sehir, address.Ilce);
    } else {
        fillIlSelect();
        fillIlceSelect("");
    }
}

        function hideAddressForm() {
            document.getElementById('addressForm').style.display = 'none';
        }

        function editAddress(addressId) {
            // AJAX ile adres bilgilerini çekip formu doldur
            fetch('get_address.php?id=' + addressId)
                .then(response => response.json())
                .then(address => {
                    showAddressForm(address);
                });
        }

        function deleteAddress(addressId) {
            if (confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
                window.location.href = 'delete_address.php?id=' + addressId;
            }
        }

        function addToCart(productId) {
            // Sepete ekleme işlemi için AJAX isteği
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ürün sepete eklendi!');
                } else {
                    alert('Ürün sepete eklenirken bir hata oluştu!');
                }
            });
        }

        function removeFromFavorites(favoriteId, btn) {
            if (confirm('Bu ürünü favorilerden çıkarmak istediğinizden emin misiniz?')) {
                fetch('remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'favorite_id=' + favoriteId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Satırı animasyonla kaldır
                        if(btn) {
                            const row = btn.closest('.wishlist-row');
                            row.style.transition = 'opacity 0.4s';
                            row.style.opacity = 0;
                            setTimeout(() => row.remove(), 400);
                        } else {
                            location.reload();
                        }
                    } else {
                        alert('Ürün favorilerden çıkarılırken bir hata oluştu!');
                    }
                });
            }
        }
    </script>
    <script src="turkiye-il-ilce.js"></script>
    <script src="adres-dropdown.js"></script>
</body>
</html> 