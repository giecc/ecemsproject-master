// jQuery'yi dinamik olarak yükle
(function loadjQuery() {
    if (typeof jQuery === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.type = 'text/javascript';
        script.onload = function() {
            console.log('jQuery başarıyla yüklendi.');
            // jQuery yüklendikten sonra kodları çalıştır
            initializeApp();
        };
        document.head.appendChild(script);
    } else {
        console.log('jQuery zaten yüklü.');
        initializeApp();
    }
})();

// Ana uygulama kodları
function initializeApp() {
/*=============Reusable css classes=========*/

/*=============image galery=========*/

function imgGallery() {
  const mainImg = document.querySelector('.details__img'),
    smallImg = document.querySelectorAll('.details__small-img');

  smallImg.forEach((img) => {
    img.addEventListener('click', function () {
      mainImg.src = this.src;
    })
  })
}

imgGallery();

    /*=============SWIPER=========*/
    $(document).ready(function() {
  var swiper = new Swiper('.categories__container', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      640: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      1024: { slidesPerView: 4 }
    }
  });
});

/*=============PRODUCTS TABS=========*/
    $(document).ready(function() {
        $('[data-target]').on('click', function() {
            const target = $($(this).data('target'));
            
            $('[data-content]').removeClass('active-tab');
            target.addClass('active-tab');
            
            $('[data-target]').removeClass('active-tab');
            $(this).addClass('active-tab');
        });
    });

    /*=============SHOP MENU=========*/
    $(document).ready(function() {
        const $shopMenu = $('.shop-menu');
        const $dropdown = $('.shop-dropdown');

        $shopMenu.on('mouseenter', function() {
            $dropdown.css({
                'display': 'block',
                'opacity': '1',
                'visibility': 'visible'
  });
});

        $shopMenu.on('mouseleave', function() {
            $dropdown.css({
                'display': 'none',
                'opacity': '0',
                'visibility': 'hidden'
            });
  });
});

/*=============cart.html=========*/

// Sepet verisi için basit bir yapı
let cart = [];

// Sepete ekleme fonksiyonu
function addToCart(product) {
    // Ürün zaten sepette var mı kontrol et
    const existingItem = cart.find(item => item.id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: 1
        });
    }

    // Sepeti güncelle ve kaydet
    updateCartDisplay();
    saveCartToLocalStorage();
    
    // Bildirim göster
    showNotification(`${product.name} sepete eklendi!`);
}

// Sepeti güncelleme fonksiyonu
function updateCartDisplay() {
        const $cartItemsContainer = $('#cart-items');
        const $cartTotalElement = $('#cart-total');
        const $cartCounters = $('.header__action-btn .count');

    // Sepet boşsa
        if ($cartItemsContainer.length === 0) return;

    if (cart.length === 0) {
            $cartItemsContainer.html('<tr><td colspan="6" class="text-center">Sepetiniz boş</td></tr>');
            if ($cartTotalElement.length) $cartTotalElement.text('0.00 TL');
            $cartCounters.text('0');
        return;
    }

    // Sepet öğelerini oluştur
        $cartItemsContainer.html(cart.map(item => {
            // Resim yolunun başındaki assets/ ifadesini kaldır
            const imagePath = item.image.replace(/^assets\//, '');
            return `
        <tr data-id="${item.id}">
            <td>
                <img src="${imagePath}" alt="${item.name}" class="table__img">
            </td>
            <td>
                <h3 class="table__title">${item.name}</h3>
            </td>
            <td>${item.price.toFixed(2)} TL</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" 
                        class="quantity" data-id="${item.id}">
            </td>
            <td>${(item.price * item.quantity).toFixed(2)} TL</td>
            <td>
                <button class="remove-item" data-id="${item.id}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
        `}).join(''));

    // Toplamı hesapla
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        if ($cartTotalElement.length) $cartTotalElement.text(`${total.toFixed(2)} TL`);

    // Sepet sayacını güncelle
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        $cartCounters.text(totalItems);
}

// LocalStorage'a kaydetme
function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// LocalStorage'dan yükleme
function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartDisplay();
    }
}

// Event listener'lar
    $(document).ready(function() {
    // Sepeti yükle
    loadCartFromLocalStorage();

    // Sepete ekleme butonları için event listener
        $(document).on('click', '.cart__btn, .add-to-cart', function() {
            const $button = $(this);
            const product = {
                id: $button.data('id'),
                name: $button.data('name'),
                price: parseFloat($button.data('price')),
                image: $button.data('image')
            };
            
            if (product.id && product.name && product.price) {
                addToCart(product);
        }
    });

    // Sepet içindeki miktar değişiklikleri
        $(document).on('change', '.quantity', function() {
            const productId = $(this).data('id');
            const newQuantity = parseInt($(this).val());
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity = newQuantity;
                updateCartDisplay();
                saveCartToLocalStorage();
        }
    });

    // Ürün silme butonları
        $(document).on('click', '.remove-item', function() {
            const productId = $(this).data('id');
            cart = cart.filter(item => item.id !== productId);
            updateCartDisplay();
            saveCartToLocalStorage();
            showNotification('Ürün sepetten kaldırıldı!');
    });
});

// Bildirim gösterme fonksiyonu
function showNotification(message) {
        const $notification = $('<div>')
            .addClass('notification')
            .text(message)
            .appendTo('body');

    setTimeout(() => {
            $notification.remove();
    }, 2000);
}

// Örnek ürün ekleme (ürün sayfalarında bu fonksiyonu kullanacaksınız)
function exampleAddToCart() {
  const product = {
    id: 'bolero1',
    name: "Women's Short Bolero",
    description: "Elegant short bolero for women",
    price: 249.99,
    image: "img/bolero1-1.png.png"
  };
  addToCart(product);
}

// Ürün sayfalarında sepete ekleme butonları
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();
    const product = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            description: $(this).data('description') || '',
            price: parseFloat($(this).data('price')),
            image: $(this).data('image')
    };
    addToCart(product);

    // Bildirim göster
    alert(`${product.name} sepete eklendi!`);
});

// Sepete ekleme işlemi
$(document).on('click', '.cart__btn', function (e) {
  e.preventDefault();
        const $button = $(this);
        const productId = $button.data('id');
        const productName = $button.data('name');
        const productPrice = $button.data('price');
        const productImage = $button.data('image');

  $.ajax({
    url: 'cart.php',
    type: 'POST',
            data: {
                urun_id: productId,
                ad: productName,
                fiyat: productPrice,
                resim: productImage
            },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        // Sepet sayacını güncelle
        $('.count').text(response.cart_count);

        // Bildirim göster
        alert('Ürün sepete eklendi!');
      }
    },
    error: function () {
      alert('Bir hata oluştu, lütfen tekrar deneyin.');
    }
  });
});

// Miktar güncelleme
$(document).on('change', '.quantity-input', function () {
  const row = $(this).closest('tr');
  const productId = row.data('id');
  const quantity = $(this).val();

  $.ajax({
    url: 'update_cart.php',
    type: 'POST',
    data: {
      urun_id: productId,
      quantity: quantity
    },
    success: function () {
      location.reload(); // Sayfayı yenile
    }
  });
});

// Ürün silme
$(document).on('click', '.remove-btn', function () {
  if (confirm('Bu ürünü sepetinizden çıkarmak istediğinize emin misiniz?')) {
    const productId = $(this).closest('tr').data('id');

    $.ajax({
      url: 'remove_from_cart.php',
      type: 'POST',
      data: { urun_id: productId },
      success: function () {
        location.reload(); // Sayfayı yenile
      }
    });
  }
});

// cart.js
$(document).ready(function () {
  // Sepete ürün ekleme
  // Sepete ürün ekleme
  $(document).on('click', '.sepete-ekle', function () {
    const urunID = $(this).data('id');

    $.ajax({
      url: 'sepet.php',
      type: 'POST',
      data: { urun_id: urunID },
      success: function (response) {
        alert('Ürün sepete eklendi!');
        // Sepet sayacını güncelle
        const sepetAdet = Object.keys($_SESSION['sepet']).length;
        $('.sepet-sayaci').text(sepetAdet);
      }
    });
  });

  // Ürün adet güncelleme
  $(document).on('change', '.adet-input', function () {
    const urunID = $(this).closest('tr').data('id');
    const yeniAdet = $(this).val();

    $.post('sepet-guncelle.php', {
      urun_id: urunID,
      adet: yeniAdet
    }, function () {
      location.reload();
    });
  });

  // Ürün silme
  $(document).on('click', '.sil-btn', function () {
    if (confirm('Bu ürünü silmek istediğinize emin misiniz?')) {
      const urunID = $(this).data('id');

      $.post('sepet-sil.php', { urun_id: urunID }, function () {
        location.reload();
      });
    }
  });

  // Miktar güncelleme
  $(document).on('change', '.quantity-input', function () {
    const row = $(this).closest('tr');
    const productId = row.data('id');
    const quantity = $(this).val();

    $.ajax({
      url: 'cart.php',
      type: 'POST',
      data: {
        action: 'update',
        product_id: productId,
        quantity: quantity
      },
      dataType: 'json',
      success: function () {
        location.reload();
      }
    });
  });

  // Ürün silme
  $(document).on('click', '.remove-btn', function () {
    if (confirm('Bu ürünü sepetinizden çıkarmak istediğinize emin misiniz?')) {
      const productId = $(this).closest('tr').data('id');

      $.ajax({
        url: 'cart.php',
        type: 'POST',
        data: {
          action: 'remove',
          product_id: productId
        },
        dataType: 'json',
        success: function () {
          location.reload();
        }
      });
    }
  });

  // Alışverişe devam et
  $('.continue-shopping').on('click', function () {
    window.location.href = 'shop.php';
  });

  // Ödemeye geç
  $('.checkout').on('click', function () {
    if ($('#cart-items tr').length > 0) {
      window.location.href = 'checkout.php';
    } else {
      alert('Sepetiniz boş!');
    }
  });
});

//login için
    $(document).ready(function() {
        $('#login-form').on('submit', function(e) {
  e.preventDefault();

            const email = $('#login-email').val().trim();
            const password = $('#login-password').val().trim();

  // Giriş bilgilerini kontrol et
  if (!email || !password) {
    alert('Lütfen tüm alanları doldurun.');
    return;
  }

            // Promise kullanarak login işlemini yap
            $.ajax({
                url: 'login.php',
      method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ email, password }),
                success: function(result) {
    if (result.success) {
      // Başarılı giriş, kullanıcıyı yönlendir
      alert('Giriş başarılı!');
      window.location.href = 'index.html';  // Anasayfaya yönlendir
    } else {
      // Başarısız giriş
      alert(result.message || 'Giriş başarısız');
    }
                },
                error: function(error) {
    console.error('Hata:', error);
    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
  }
            });
        });
});

// contact.html dosyanıza bu scripti ekleyin
    $(document).ready(function() {
        $('#contactForm').on('submit', function(e) {
  e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const responseMessage = $('#responseMessage');

  // Buton durumunu güncelle
            submitBtn.prop('disabled', true);
            const originalText = submitBtn.text();
            submitBtn.html('<span class="spinner"></span> Gönderiliyor...');

  // Response mesajını temizle
            responseMessage.hide();
            responseMessage.text('');
            responseMessage.removeClass();

            // Form verilerini al
            const formData = new FormData(form[0]);

            // AJAX isteği gönder
            $.ajax({
                url: 'process_contact.php',
      method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 10000,
                success: function(data) {
    // Yanıtı göster
                    responseMessage.text(data.message);
                    responseMessage.addClass(data.success ? 'success' : 'error');

    if (data.success) {
                        form.trigger('reset');
    }
                },
                error: function(error) {
    console.error('Form gönderim hatası:', error);
                    responseMessage.text('İşlem sırasında bir hata oluştu. Lütfen tekrar deneyin.');
                    responseMessage.addClass('error');
                },
                complete: function() {
                    responseMessage.show();
                    submitBtn.prop('disabled', false);
                    submitBtn.text(originalText);

    // Mesajı 8 saniye sonra gizle
    setTimeout(() => {
                        if (responseMessage.text().includes('başarıyla')) {
                            responseMessage.hide();
      }
    }, 8000);
  }
            });
        });
    });

    // === HESABIM SEKME GEÇİŞLERİ ===
    $(document).ready(function() {
        const accountTabs = $('.account-nav a');
        const tabContents = $('.account-tab');

        accountTabs.each(function() {
            $(this).on('click', function(e) {
                // Sadece logout linki hariç
                if ($(this).hasClass('logout')) return;
                e.preventDefault();
                // Tüm sekmelerden active kaldır
                accountTabs.removeClass('active');
                tabContents.removeClass('active');
                // Tıklanan sekmeye active ekle
                $(this).addClass('active');
                // İlgili içeriği göster
                const targetId = $(this).attr('href').replace('#', '');
                const targetContent = $('#' + targetId);
                if (targetContent.length) targetContent.addClass('active');
            });
        });
    });

    // === HESABIM SEKME GEÇİŞLERİ ===
    $(document).ready(function() {
        const accountTabs = $('.account-nav a');
        const tabContents = $('.account-tab');

        accountTabs.each(function() {
            $(this).on('click', function(e) {
                // Sadece logout linki hariç
                if ($(this).hasClass('logout')) return;
                e.preventDefault();
                // Tüm sekmelerden active kaldır
                accountTabs.removeClass('active');
                tabContents.removeClass('active');
                // Tıklanan sekmeye active ekle
                $(this).addClass('active');
                // İlgili içeriği göster
                const targetId = $(this).attr('href').replace('#', '');
                const targetContent = $('#' + targetId);
                if (targetContent.length) targetContent.addClass('active');
            });
        });
});

// === HESABIM SEKME GEÇİŞLERİ ===
document.addEventListener('DOMContentLoaded', function() {
    const accountTabs = document.querySelectorAll('.account-nav a');
    const tabContents = document.querySelectorAll('.account-tab');

    accountTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            // Sadece logout linki hariç
            if (tab.classList.contains('logout')) return;
            e.preventDefault();
            // Tüm sekmelerden active kaldır
            accountTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            // Tıklanan sekmeye active ekle
            tab.classList.add('active');
            // İlgili içeriği göster
            const targetId = tab.getAttribute('href').replace('#', '');
            const targetContent = document.getElementById(targetId);
            if (targetContent) targetContent.classList.add('active');
        });
    });
});

// Form Submissions
    $(document).ready(function() {
        $('.account-form').on('submit', function(e) {
        e.preventDefault();
        
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const responseMessage = form.find('.response-message');

            // Buton durumunu güncelle
            submitBtn.prop('disabled', true);
            const originalText = submitBtn.text();
            submitBtn.html('<span class="spinner"></span> Gönderiliyor...');

            // Form verilerini al
            const formData = new FormData(form[0]);

            // AJAX isteği gönder
            $.ajax({
                url: 'process_account.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
        alert('Bilgileriniz başarıyla güncellendi!');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        alert(response.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                    }
                },
                error: function() {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.text(originalText);
                }
            });
    });
});

// Address Actions
    $(document).ready(function() {
        $('.address-actions button').on('click', function() {
            const $button = $(this);
            const $addressItem = $button.closest('.address-item');
            const addressId = $addressItem.data('id');

            if ($button.hasClass('btn--danger')) {
            if (confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
                    $.ajax({
                        url: 'delete_address.php',
                        method: 'POST',
                        data: { address_id: addressId },
                        success: function(response) {
                            if (response.success) {
                                $addressItem.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert(response.message || 'Adres silinirken bir hata oluştu.');
                            }
                        },
                        error: function() {
                            alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                        }
                    });
            }
        } else {
                // Adres düzenleme formunu göster
            alert('Adres düzenleme özelliği yakında eklenecek!');
        }
    });
});

// Add to Cart from Favorites
    $(document).ready(function() {
        $('.favorite-item .btn').on('click', function() {
            const $button = $(this);
            const productId = $button.data('id');

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: { product_id: productId },
                success: function(response) {
                    if (response.success) {
        alert('Ürün sepete eklendi!');
                        // Sepet sayacını güncelle
                        $('.cart-count').text(response.cart_count);
                    } else {
                        alert(response.message || 'Ürün sepete eklenirken bir hata oluştu.');
                    }
                },
                error: function() {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            });
    });
});

// Favoriler Sayfası İşlevselliği
document.addEventListener('DOMContentLoaded', function() {
    const favoritesGrid = document.querySelector('.favorites-grid');
    const favoritesEmpty = document.querySelector('.favorites-empty');
    
    if (favoritesGrid) {
        // Favorilerden Kaldırma
        const removeButtons = document.querySelectorAll('.favorite-item__remove');
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const favoriteItem = this.closest('.favorite-item');
                const productId = favoriteItem.dataset.id;
                
                // Animasyonlu kaldırma
                favoriteItem.style.opacity = '0';
                favoriteItem.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    favoriteItem.remove();
                    
                    // Favori kalmadıysa boş durum mesajını göster
                    if (favoritesGrid.children.length === 0) {
                        favoritesGrid.style.display = 'none';
                        favoritesEmpty.style.display = 'block';
                    }
                }, 300);
                
                // API çağrısı burada yapılacak
                console.log('Ürün favorilerden kaldırıldı:', productId);
            });
        });
        
        // Sepete Ekleme
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const favoriteItem = this.closest('.favorite-item');
                
                // Buton durumunu güncelle
                this.innerHTML = '<i class="fas fa-check"></i> Sepete Eklendi';
                this.disabled = true;
                this.style.backgroundColor = '#4CAF50';
                
                // 2 saniye sonra butonu eski haline getir
                setTimeout(() => {
                    this.innerHTML = 'Sepete Ekle';
                    this.disabled = false;
                    this.style.backgroundColor = '';
                }, 2000);
                
                // API çağrısı burada yapılacak
                console.log('Ürün sepete eklendi:', productId);
            });
        });
    }
});

// Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    // Favori ürünleri localStorage'dan al
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    updateWishlistCount();

    // Favori butonlarına tıklama olayı ekle
    const wishlistButtons = document.querySelectorAll('.action__btn i.fa-heart');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productItem = this.closest('.product__item');
            const product = {
                id: productItem.dataset.id,
                title: productItem.querySelector('.product__title').textContent,
                price: productItem.querySelector('.new__price').textContent,
                image: productItem.querySelector('.product__img').src
            };

            toggleWishlist(product);
        });
    });

    // Favori sayfasındaki kaldır butonlarına tıklama olayı ekle
    const removeButtons = document.querySelectorAll('.wishlist__btn.remove');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const wishlistItem = this.closest('.wishlist__item');
            const productId = wishlistItem.dataset.id;
            removeFromWishlist(productId);
            wishlistItem.remove();
        });
    });

    // Sepete ekle butonlarına tıklama olayı ekle
    const addToCartButtons = document.querySelectorAll('.wishlist__btn.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const wishlistItem = this.closest('.wishlist__item');
            const productId = wishlistItem.dataset.id;
            addToCart(productId);
        });
    });
});

// Favori listesine ekle/çıkar
function toggleWishlist(product) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const index = wishlist.findIndex(item => item.id === product.id);

    if (index === -1) {
        wishlist.push(product);
        this.classList.remove('fa-regular');
        this.classList.add('fa-solid');
    } else {
        wishlist.splice(index, 1);
        this.classList.remove('fa-solid');
        this.classList.add('fa-regular');
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistCount();
}

// Favori listesinden kaldır
function removeFromWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    wishlist = wishlist.filter(item => item.id !== productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistCount();
}

// Favori sayısını güncelle
function updateWishlistCount() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const countElements = document.querySelectorAll('.header__action-btn .count');
    countElements.forEach(element => {
        element.textContent = wishlist.length;
    });
}

// Sepete ekle
function addToCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const product = wishlist.find(item => item.id === productId);

    if (product) {
        cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        removeFromWishlist(productId);
        alert('Ürün sepete eklendi!');
    }
}

// Product Details Page
const productDetails = () => {
    // Small Images
    const smallImages = document.querySelectorAll('.details__small-img');
    const mainImage = document.querySelector('.details__main-img');

    smallImages.forEach(img => {
        img.addEventListener('click', () => {
            // Remove active class from all images
            smallImages.forEach(img => img.classList.remove('active'));
            // Add active class to clicked image
            img.classList.add('active');
            // Update main image
            mainImage.src = img.src;
        });
    });

    // Size Selection
    const sizeLinks = document.querySelectorAll('.size__link');
    
    sizeLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Remove active class from all sizes
            sizeLinks.forEach(link => link.classList.remove('size-active'));
            // Add active class to clicked size
            link.classList.add('size-active');
        });
    });

    // Quantity
    const minusBtn = document.querySelector('.quantity__btn.minus');
    const plusBtn = document.querySelector('.quantity__btn.plus');
    const quantityInput = document.querySelector('.quantity__input');

    minusBtn.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });

    plusBtn.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value < 10) {
            quantityInput.value = value + 1;
        }
    });

    // Add to Cart
    const addToCartBtn = document.querySelector('.add-to-cart');
    
    addToCartBtn.addEventListener('click', () => {
        const product = {
            id: 'T07',
            name: document.querySelector('.details__title').textContent,
            price: document.querySelector('.new__price').textContent,
            size: document.querySelector('.size__link.size-active').textContent,
            quantity: parseInt(quantityInput.value),
            image: mainImage.src
        };

        // Get existing cart items
        let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
        
        // Check if product already exists in cart
        const existingItemIndex = cartItems.findIndex(item => 
            item.id === product.id && item.size === product.size
        );

        if (existingItemIndex > -1) {
            // Update quantity if product exists
            cartItems[existingItemIndex].quantity += product.quantity;
        } else {
            // Add new product if it doesn't exist
            cartItems.push(product);
        }

        // Save updated cart
        localStorage.setItem('cartItems', JSON.stringify(cartItems));

        // Update cart count
        updateCartCount();

        // Show success message
        showNotification('Ürün sepete eklendi!');
    });

    // Add to Wishlist
    const addToWishlistBtn = document.querySelector('.add-to-wishlist');
    
    addToWishlistBtn.addEventListener('click', () => {
        const product = {
            id: 'T07',
            name: document.querySelector('.details__title').textContent,
            price: document.querySelector('.new__price').textContent,
            image: mainImage.src
        };

        // Get existing wishlist items
        let wishlistItems = JSON.parse(localStorage.getItem('wishlistItems')) || [];
        
        // Check if product already exists in wishlist
        const existingItemIndex = wishlistItems.findIndex(item => item.id === product.id);

        if (existingItemIndex > -1) {
            // Remove from wishlist if already exists
            wishlistItems.splice(existingItemIndex, 1);
            showNotification('Ürün favorilerden çıkarıldı!');
        } else {
            // Add to wishlist if doesn't exist
            wishlistItems.push(product);
            showNotification('Ürün favorilere eklendi!');
        }

        // Save updated wishlist
        localStorage.setItem('wishlistItems', JSON.stringify(wishlistItems));

        // Update wishlist count
        updateWishlistCount();
    });
};

// Initialize product details page
if (document.querySelector('.details')) {
    productDetails();
}

// Ürün kartlarındaki göz ikonuna tıklama olayı
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.action__btn i.fa-eye');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productItem = this.closest('.product__item');
            const productId = productItem.dataset.id;
            
            // Ürün detay sayfasına yönlendir
            window.location.href = `details.html?id=${productId}`;
        });
    });
});

// Ürün detay sayfasına yönlendirme
document.addEventListener('DOMContentLoaded', function() {
    // Göz ikonuna tıklama
    const viewButtons = document.querySelectorAll('.action__btn.view-product');
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            window.location.href = `details.html?id=${productId}`;
        });
    });

    // Ürün resmine tıklama
    const productImages = document.querySelectorAll('.product__images');
    productImages.forEach(image => {
        image.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.closest('.product__item').dataset.id;
            window.location.href = `details.html?id=${productId}`;
        });
    });

    // Ürün başlığına tıklama
    const productTitles = document.querySelectorAll('.product__title');
    productTitles.forEach(title => {
        title.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.closest('.product__item').dataset.id;
            window.location.href = `details.html?id=${productId}`;
        });
    });
});
}
