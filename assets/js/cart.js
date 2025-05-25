// Global cart array
let cart = [];

// Load cart from localStorage
function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
    }
}

// Save cart to localStorage
function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Update cart display
function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer) return;

    cartItemsContainer.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover;"></td>
            <td>${item.name}</td>
            <td>${item.price} TL</td>
            <td>
                <input type="number" class="miktar-input" value="${item.quantity}" min="1" 
                    data-id="${index}" style="width: 60px;">
            </td>
            <td>${(item.price * item.quantity).toFixed(2)} TL</td>
            <td>
                <button onclick="removeFromCart(${index})" class="btn-remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        cartItemsContainer.appendChild(row);
        total += item.price * item.quantity;
    });

    if (cartTotalElement) {
        cartTotalElement.textContent = total.toFixed(2) + ' TL';
    }

    // Update cart count in header
    const cartCountElements = document.querySelectorAll('.header__action-btn .count');
    cartCountElements.forEach(element => {
        element.textContent = cart.length;
    });
}

// Add to cart
function addToCart(product) {
    const existingItemIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: 1
        });
    }
    
    saveCartToLocalStorage();
    updateCartDisplay();
    showNotification('Ürün sepete eklendi!');
}

// Remove from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    saveCartToLocalStorage();
    updateCartDisplay();
    showNotification('Ürün sepetten çıkarıldı!');
}

// Update quantity
function updateQuantity(index, newQuantity) {
    if (newQuantity < 1) return;
    
    cart[index].quantity = parseInt(newQuantity);
    saveCartToLocalStorage();
    updateCartDisplay();
}

// Show notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 2000);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    loadCartFromLocalStorage();
    updateCartDisplay();

    // Quantity change handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('miktar-input')) {
            const index = e.target.dataset.id;
            const newQuantity = e.target.value;
            updateQuantity(index, newQuantity);
        }
    });
});

// Add to cart button click handler
$(document).on('click', '.cart__btn', function(e) {
    e.preventDefault();
    const $productCard = $(this).closest('.product__item');
    const productId = $(this).data('id');
    // Get product details from the product card
    const product = {
        id: productId,
        name: $productCard.find('.product__title').text().trim(),
        price: parseFloat($productCard.find('.new__price').text().replace('TL', '').trim()),
        image: $(this).data('image')
    };
    addToCart(product);
});