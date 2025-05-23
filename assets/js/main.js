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














document.addEventListener('DOMContentLoaded', function () {
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






/*=============SWIPER PRODUCTS=========*/



/*=============PRODUCTS TABS=========*/
const tabs = document.querySelectorAll('[data-target]'),
  tabContents = document.querySelectorAll('[data-content]');


tabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    const target = document.querySelector(tab.dataset.target);
    console.log(target);

    tabContents.forEach((tabContent) => {
      tabContent.classList.remove('active-tab');
    });


    target.classList.add('active-tab');


    tabs.forEach((tab) => {
      tab.classList.remove('active-tab');
    });

    tab.classList.add('active-tab');

  });
});








document.addEventListener('DOMContentLoaded', function () {
  const shopMenu = document.querySelector('shop-menu');
  const dropdown = document.querySelector('shop-dropdown');

  shopMenu.addEventListener('mouseenter', function () {
    dropdown.style.display = 'block';
    dropdown.style.opacity = '1';
    dropdown.style.visibility = 'visible';
  });

  shopMenu.addEventListener('mouseleave', function () {
    dropdown.style.display = 'none';
    dropdown.style.opacity = '0';
    dropdown.style.visibility = 'hidden';
  });
});




/*=============cart.html=========*/

// Sepet verisi için basit bir yapı
let cart = [];

// Sepeti güncelleme fonksiyonu
function updateCartDisplay() {
  const cartItemsContainer = document.getElementById('cart-items');
  const cartTotalElement = document.getElementById('cart-total');

  // Sepet boşsa
  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<tr><td colspan="6" class="text-center">Sepetiniz boş</td></tr>';
    cartTotalElement.textContent = '0.00 TL';
    return;
  }

  // Sepet öğelerini oluştur
  cartItemsContainer.innerHTML = cart.map(item => `
        <tr data-id="${item.id}">
            <td>
                <img src="${item.image}" alt="${item.name}" class="table__img">
            </td>
            <td>
                <h3 class="table__title">${item.name}</h3>
                <p class="table__description">${item.description}</p>
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
    `).join('');

  // Toplamı hesapla
  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  cartTotalElement.textContent = `${total.toFixed(2)} TL`;

  // Sepet sayacını güncelle
  updateCartCounter();
}

// Sepet sayacını güncelleme
function updateCartCounter() {
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  document.querySelectorAll('.header__action-btn .count').forEach(el => {
    el.textContent = totalItems;
  });
}

// Ürün ekleme fonksiyonu
function addToCart(product) {
  const existingItem = cart.find(item => item.id === product.id);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({ ...product, quantity: 1 });
  }

  updateCartDisplay();
  saveCartToLocalStorage();
}

// Ürün silme fonksiyonu
function removeFromCart(productId) {
  cart = cart.filter(item => item.id !== productId);
  updateCartDisplay();
  saveCartToLocalStorage();
}

// Miktar değiştirme fonksiyonu
function updateQuantity(productId, newQuantity) {
  const item = cart.find(item => item.id === productId);
  if (item) {
    item.quantity = parseInt(newQuantity) || 1;
    updateCartDisplay();
    saveCartToLocalStorage();
  }
}

// LocalStorage'e kaydetme
function saveCartToLocalStorage() {
  localStorage.setItem('cart', JSON.stringify(cart));
}

// LocalStorage'den yükleme
function loadCartFromLocalStorage() {
  const savedCart = localStorage.getItem('cart');
  if (savedCart) {
    cart = JSON.parse(savedCart);
    updateCartDisplay();
  }
}

// Event listener'lar
document.addEventListener('DOMContentLoaded', () => {
  loadCartFromLocalStorage();

  // Sepet içindeki miktar değişiklikleri
  document.addEventListener('change', e => {
    if (e.target.classList.contains('quantity')) {
      const productId = e.target.dataset.id;
      updateQuantity(productId, e.target.value);
    }
  });

  // Ürün silme butonları
  document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
      const productId = e.target.dataset.id || e.target.closest('.remove-item').dataset.id;
      removeFromCart(productId);
    }
  });
});

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
document.addEventListener('click', e => {
  if (e.target.classList.contains('add-to-cart')) {
    const product = {
      id: e.target.dataset.id,
      name: e.target.dataset.name,
      description: e.target.dataset.description || '',
      price: parseFloat(e.target.dataset.price),
      image: e.target.dataset.image
    };
    addToCart(product);

    // Bildirim göster
    alert(`${product.name} sepete eklendi!`);
  }
});




// Sepete ekleme işlemi
$(document).on('click', '.cart__btn', function (e) {
  e.preventDefault();
  const productId = $(this).data('id');

  $.ajax({
    url: 'cart.php',
    type: 'POST',
    data: { urun_id: productId },
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

document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();

  const email = document.getElementById('login-email').value.trim();
  const password = document.getElementById('login-password').value.trim();

  // Giriş bilgilerini kontrol et
  if (!email || !password) {
    alert('Lütfen tüm alanları doldurun.');
    return;
  }

  try {
    const response = await fetch('login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });

    const result = await response.json();

    if (result.success) {
      // Başarılı giriş, kullanıcıyı yönlendir
      alert('Giriş başarılı!');
      window.location.href = 'index.html';  // Anasayfaya yönlendir
    } else {
      // Başarısız giriş
      alert(result.message || 'Giriş başarısız');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
  }
});




// contact.html dosyanıza bu scripti ekleyin
document.getElementById('contactForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const responseMessage = document.getElementById('responseMessage');

  // Buton durumunu güncelle
  submitBtn.disabled = true;
  const originalText = submitBtn.textContent;
  submitBtn.innerHTML = '<span class="spinner"></span> Gönderiliyor...';

  // Response mesajını temizle
  responseMessage.style.display = 'none';
  responseMessage.textContent = '';
  responseMessage.className = '';

  try {
    const formData = new FormData(form);

    // Fetch timeout ayarı (10 saniye)
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000);

    const response = await fetch('process_contact.php', {
      method: 'POST',
      body: formData,
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    // HTTP hata kontrolü
    if (!response.ok) {
      throw new Error(`HTTP hatası! Durum: ${response.status}`);
    }

    // Yanıtı işle
    const responseText = await response.text();

    if (!responseText.trim()) {
      throw new Error('Sunucu boş yanıt verdi');
    }

    let data;
    try {
      data = JSON.parse(responseText);
    } catch (e) {
      console.error('JSON ayrıştırma hatası:', e, 'Yanıt:', responseText);
      throw new Error('Sunucu yanıtı geçersiz');
    }

    // Yanıtı göster
    responseMessage.textContent = data.message;
    responseMessage.className = data.success ? 'success' : 'error';

    if (data.success) {
      form.reset();
    }

  } catch (error) {
    console.error('Form gönderim hatası:', error);
    responseMessage.textContent = 'İşlem sırasında bir hata oluştu. Lütfen tekrar deneyin.';
    responseMessage.className = 'error';
  } finally {
    responseMessage.style.display = 'block';
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;

    // Mesajı 8 saniye sonra gizle
    setTimeout(() => {
      if (responseMessage.textContent.includes('başarıyla')) {
        responseMessage.style.display = 'none';
      }
    }, 8000);
  }
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
const accountForms = document.querySelectorAll('.account-form');

accountForms.forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Here you would typically send the form data to your backend
        // For now, we'll just show a success message
        alert('Bilgileriniz başarıyla güncellendi!');
    });
});

// Address Actions
const addressActions = document.querySelectorAll('.address-actions button');

addressActions.forEach(button => {
    button.addEventListener('click', () => {
        if (button.classList.contains('btn--danger')) {
            if (confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
                // Here you would typically send a delete request to your backend
                button.closest('.address-item').remove();
            }
        } else {
            // Here you would typically show an edit form
            alert('Adres düzenleme özelliği yakında eklenecek!');
        }
    });
});

// Add to Cart from Favorites
const addToCartButtons = document.querySelectorAll('.favorite-item .btn');

addToCartButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Here you would typically add the item to cart
        alert('Ürün sepete eklendi!');
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

// Bildirim gösterme fonksiyonu
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 2000);
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
