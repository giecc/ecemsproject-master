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
