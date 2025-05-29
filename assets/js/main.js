// Global değişkenler
let cart = [];

// Sepet işlemleri için yardımcı fonksiyonlar
function loadCartFromLocalStorage() {
    console.log('Loading cart from localStorage...'); // Debug log
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartDisplay();
    }
}

function saveCartToLocalStorage() {
    console.log('Saving cart to localStorage:', cart); // Debug log
    localStorage.setItem('cart', JSON.stringify(cart));
}

function updateCartDisplay() {
    console.log('Updating cart display...'); // Debug log
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const cartCounters = document.querySelectorAll('.header__action-btn .count');

    if (!cartItemsContainer) {
        // Sadece sayacı güncelle
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCounters.forEach(counter => counter.textContent = totalItems);
        return;
    }

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<tr><td colspan="6" class="text-center">Sepetiniz boş</td></tr>';
        if (cartTotalElement) cartTotalElement.textContent = '0.00 TL';
        cartCounters.forEach(counter => counter.textContent = '0');
        return;
    }

    // Sepet öğelerini oluştur
    cartItemsContainer.innerHTML = cart.map(item => {
        // Resim yolunu düzelt
        let imagePath = item.image;
        if (imagePath.includes('.jpg')) {
            imagePath = imagePath.replace('.jpg', '.png.png');
        }
        
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
        `;
    }).join('');

    // Toplamı hesapla
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    if (cartTotalElement) cartTotalElement.textContent = `${total.toFixed(2)} TL`;

    // Sepet sayacını güncelle
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCounters.forEach(counter => counter.textContent = totalItems);
}

function addToCart(product) {
    console.log('Adding to cart:', product); // Debug log

    if (!product || !product.id || !product.name || !product.price) {
        console.error('Invalid product data:', product);
        return;
    }

    const existingItem = cart.find(item => item.id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            image: product.image,
            quantity: 1
        });
    }

    updateCartDisplay();
    saveCartToLocalStorage();
    showNotification(`${product.name} sepete eklendi!`);
    
    // Sepete eklendikten sonra sepet sayfasına yönlendir
    window.location.href = 'cart.html';
}

function removeFromCart(productId) {
    console.log('Removing from cart:', productId); // Debug log
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
    saveCartToLocalStorage();
    showNotification('Ürün sepetten kaldırıldı!');
}

function clearCart() {
    if (confirm('Sepeti temizlemek istediğinize emin misiniz?')) {
        cart = [];
        updateCartDisplay();
        saveCartToLocalStorage();
        showNotification('Sepet temizlendi!');
    }
}

function checkout() {
    if (cart.length === 0) {
        alert('Sepetiniz boş!');
        return;
    }
    // Ödeme sayfasına yönlendir
    window.location.href = 'checkout.html';
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 2000);
}

// Sayfa yüklendiğinde çalışacak kod
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing cart...'); // Debug log

    // Resim galerisi
    const mainImg = document.querySelector('.details__img');
    const smallImg = document.querySelectorAll('.details__small-img');

    if (mainImg && smallImg.length > 0) {
        smallImg.forEach((img) => {
            img.addEventListener('click', function () {
                mainImg.src = this.src;
            });
        });
    }

    // Swiper slider
    if (typeof Swiper !== 'undefined' && document.querySelector('.categories__container')) {
        new Swiper('.categories__container', {
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
    }

    // Tab geçişleri
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    if (tabs.length > 0 && forms.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                forms.forEach(f => f.classList.remove('active'));

                tab.classList.add('active');
                const formId = tab.getAttribute('data-tab') + '-form';
                const targetForm = document.getElementById(formId);
                if (targetForm) targetForm.classList.add('active');
            });
        });
    }

    // Shop menü
    const shopMenu = document.querySelector('.shop-menu');
    const dropdown = document.querySelector('.shop-dropdown');

    if (shopMenu && dropdown) {
        shopMenu.addEventListener('mouseenter', function() {
            dropdown.style.display = 'block';
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
        });

        shopMenu.addEventListener('mouseleave', function() {
            dropdown.style.display = 'none';
            dropdown.style.opacity = '0';
            dropdown.style.visibility = 'hidden';
        });
    }

    // Sepeti yükle
    loadCartFromLocalStorage();

    // Sepete ekleme butonları için event listener
    document.addEventListener('click', function(e) {
        const addToCartButton = e.target.closest('.add-to-cart');
        if (addToCartButton) {
            console.log('Add to cart button clicked:', addToCartButton); // Debug log
            
            const product = {
                id: addToCartButton.dataset.id,
                name: addToCartButton.dataset.name,
                price: addToCartButton.dataset.price,
                image: addToCartButton.dataset.image
            };
            
            console.log('Product data:', product); // Debug log
            
            if (product.id && product.name && product.price) {
                addToCart(product);
            } else {
                console.error('Missing product data:', product);
            }
        }

        // Ürün silme butonu için event listener
        if (e.target.closest('.remove-item')) {
            const button = e.target.closest('.remove-item');
            const productId = button.dataset.id;
            removeFromCart(productId);
        }
    });

    // Miktar değişiklikleri için event listener
    document.addEventListener('change', function(e) {
        if (e.target.matches('.quantity')) {
            const productId = e.target.dataset.id;
            const newQuantity = parseInt(e.target.value);
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity = newQuantity;
                updateCartDisplay();
                saveCartToLocalStorage();
            }
        }
    });
});

// ... existing code ...
