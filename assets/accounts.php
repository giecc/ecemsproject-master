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
                <p class="header__alert-news">
                    Yeni müşterilere özel %10 indirim: HOSGELDİN10
                </p>
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
                        <a href="index.html" class="nav__link">Ana Sayfa</a>
                    </li>
                    <li class="nav__item">
                        <a href="shop.html" class="nav__link">Mağaza</a>
                    </li>
                    <li class="nav__item">
                        <a href="accounts.php" class="nav__link active-link">Hesabım</a>
                    </li>
                    <li class="nav__item">
                        <a href="iletisim.html" class="nav__link">İletişim</a>
                    </li>
                </ul>
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
                        $sql = "SELECT s.*, a.AdresBasligi, a.Adres, a.Sehir, a.Ilce 
                               FROM Siparişler s 
                               INNER JOIN Adresler a ON s.AdresID = a.AdresID 
                               WHERE s.KullaniciID = ? 
                               ORDER BY s.SiparisTarihi DESC";
                        $params = array($_SESSION['user_id']);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        ?>
                        <div class="orders-list">
                            <?php
                            if ($stmt) {
                                while ($order = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    // Sipariş detaylarını çek
                                    $details_sql = "SELECT sd.*, u.UrunAdi, u.UrunResim 
                                                  FROM SiparisDetaylari sd 
                                                  INNER JOIN Urunler u ON sd.UrunID = u.UrunID 
                                                  WHERE sd.SiparisID = ?";
                                    $details_params = array($order['SiparisID']);
                                    $details_stmt = sqlsrv_query($conn, $details_sql, $details_params);
                                    ?>
                                    <div class="order-item">
                                        <div class="order-header">
                                            <span class="order-number">Sipariş #<?php echo $order['SiparisID']; ?></span>
                                            <span class="order-date"><?php echo date_format($order['SiparisTarihi'], 'd.m.Y H:i'); ?></span>
                                            <span class="order-status"><?php echo htmlspecialchars($order['SiparisDurumu']); ?></span>
                                        </div>
                                        <div class="order-address">
                                            <h4>Teslimat Adresi</h4>
                                            <p><?php echo htmlspecialchars($order['AdresBasligi']); ?></p>
                                            <p><?php echo htmlspecialchars($order['Adres']); ?></p>
                                            <p><?php echo htmlspecialchars($order['Ilce'] . '/' . $order['Sehir']); ?></p>
                                        </div>
                                        <div class="order-products">
                                            <?php
                                            if ($details_stmt) {
                                                while ($detail = sqlsrv_fetch_array($details_stmt, SQLSRV_FETCH_ASSOC)) {
                                                    ?>
                                                    <div class="product-item">
                                                        <img src="<?php echo htmlspecialchars($detail['UrunResim']); ?>" alt="<?php echo htmlspecialchars($detail['UrunAdi']); ?>">
                                                        <div class="product-info">
                                                            <h4><?php echo htmlspecialchars($detail['UrunAdi']); ?></h4>
                                                            <p>Adet: <?php echo $detail['Adet']; ?></p>
                                                            <p>Birim Fiyat: <?php echo number_format($detail['BirimFiyat'], 2); ?> TL</p>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="order-total">
                                            <p>Toplam Tutar: <?php echo number_format($order['ToplamTutar'], 2); ?> TL</p>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </section>

                    <!-- Favorilerim -->
                    <section id="favorites" class="account-tab">
                        <h2>Favorilerim</h2>
                        <?php
                        // Favorileri veritabanından çek
                        $sql = "SELECT f.*, u.* FROM Favoriler f 
                               INNER JOIN Urunler u ON f.UrunID = u.UrunID 
                               WHERE f.KullaniciID = ?";
                        $params = array($_SESSION['user_id']);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        ?>
                        <div class="favorites-grid">
                            <?php
                            if ($stmt) {
                                while ($favorite = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    ?>
                                    <div class="product-card">
                                        <img src="<?php echo htmlspecialchars($favorite['UrunResim']); ?>" alt="<?php echo htmlspecialchars($favorite['UrunAdi']); ?>">
                                        <h4><?php echo htmlspecialchars($favorite['UrunAdi']); ?></h4>
                                        <p class="price"><?php echo number_format($favorite['Fiyat'], 2); ?> TL</p>
                                        <button class="btn btn--primary" onclick="addToCart(<?php echo $favorite['UrunID']; ?>)">Sepete Ekle</button>
                                        <button class="btn btn--secondary" onclick="removeFromFavorites(<?php echo $favorite['FavoriID']; ?>)">Favorilerden Çıkar</button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
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
                        <div class="addresses-list">
                            <?php
                            if ($stmt) {
                                while ($address = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    ?>
                                    <div class="address-item">
                                        <h4><?php echo htmlspecialchars($address['AdresBasligi']); ?></h4>
                                        <p><?php echo htmlspecialchars($address['Adres']); ?></p>
                                        <p><?php echo htmlspecialchars($address['Ilce'] . '/' . $address['Sehir']); ?></p>
                                        <p>Posta Kodu: <?php echo htmlspecialchars($address['PostaKodu']); ?></p>
                                        <div class="address-actions">
                                            <button class="btn btn--small" onclick="editAddress(<?php echo $address['AdresID']; ?>)">Düzenle</button>
                                            <button class="btn btn--small btn--danger" onclick="deleteAddress(<?php echo $address['AdresID']; ?>)">Sil</button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        
                        <button class="btn btn--primary" onclick="showAddressForm()">Yeni Adres Ekle</button>
                        
                        <!-- Yeni Adres Ekleme Formu -->
                        <div id="addressForm" style="display: none;" class="address-form">
                            <h3>Yeni Adres Ekle</h3>
                            <form action="add_address.php" method="POST">
                                <div class="form-group">
                                    <label>Adres Başlığı</label>
                                    <input type="text" name="address_title" required>
                                </div>
                                <div class="form-group">
                                    <label>Adres</label>
                                    <textarea name="address" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Şehir</label>
                                    <input type="text" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label>İlçe</label>
                                    <input type="text" name="district" required>
                                </div>
                                <div class="form-group">
                                    <label>Posta Kodu</label>
                                    <input type="text" name="postal_code" required>
                                </div>
                                <button type="submit" class="btn btn--primary">Adresi Kaydet</button>
                                <button type="button" class="btn btn--secondary" onclick="hideAddressForm()">İptal</button>
                            </form>
                        </div>
                    </section>

                    <!-- Şifre Değiştir -->
                    <section id="password" class="account-tab">
                        <h2>Şifre Değiştir</h2>
                        <form class="account-form">
                            <div class="form-group">
                                <label>Mevcut Şifre</label>
                                <input type="password" name="current-password" required>
                            </div>
                            <div class="form-group">
                                <label>Yeni Şifre</label>
                                <input type="password" name="new-password" required>
                            </div>
                            <div class="form-group">
                                <label>Yeni Şifre (Tekrar)</label>
                                <input type="password" name="confirm-password" required>
                            </div>
                            <button type="submit" class="btn btn--primary">Şifreyi Değiştir</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <!--============footer==========-->
    <footer class="footer">
        <div class="footer__container container">
            <div class="footer__content">
                <div class="footer__data">
                    <h3 class="footer__subtitle">Hızlı Erişim</h3>
                    <ul>
                        <li><a href="index.html">Ana Sayfa</a></li>
                        <li><a href="shop.html">Mağaza</a></li>
                        <li><a href="accounts.php">Hesabım</a></li>
                        <li><a href="iletisim.html">İletişim</a></li>
                    </ul>
                </div>

                <div class="footer__data">
                    <h3 class="footer__subtitle">İletişim</h3>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> Fethiye / MUĞLA
                    </p>
                    <p>
                        <i class="fas fa-phone"></i> 0533 773 25 05
                    </p>
                    <p>
                        <i class="fas fa-envelope"></i> info@ewa.com
                    </p>
                </div>

                <div class="footer__data">
                    <h3 class="footer__subtitle">Bizi Takip Edin</h3>
                    <div class="footer__social">
                        <a href="#" class="footer__social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="footer__social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="footer__social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer__rights">
                <p class="footer__copy">&#169; 2024 EWA. Tüm hakları saklıdır.</p>
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

        function showAddressForm() {
            document.getElementById('addressForm').style.display = 'block';
        }

        function hideAddressForm() {
            document.getElementById('addressForm').style.display = 'none';
        }

        function deleteAddress(addressId) {
            if (confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
                window.location.href = 'delete_address.php?id=' + addressId;
            }
        }

        function editAddress(addressId) {
            window.location.href = 'edit_address.php?id=' + addressId;
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

        function removeFromFavorites(favoriteId) {
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
                        location.reload();
                    } else {
                        alert('Ürün favorilerden çıkarılırken bir hata oluştu!');
                    }
                });
            }
        }
    </script>
</body>
</html> 