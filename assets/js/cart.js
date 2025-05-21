$(document).ready(function () {
    // Sepete ürün ekleme
    $(document).on('click', '.cart__btn', function (e) {
        e.preventDefault();
        var urun_id = $(this).data('id');

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { urun_id: urun_id },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    sepetSayaciniGuncelle();
                    alert('Ürün sepete eklendi!');
                }
            }
        });
    });

    // Sepet sayacını güncelleme fonksiyonu
    function sepetSayaciniGuncelle() {
        $.ajax({
            url: 'cart.php',
            type: 'GET',
            data: { get_cart: true },
            success: function (response) {
                var count = Object.keys(response).length;
                $('.header__action-btn .count').text(count);
            },
            dataType: 'json'
        });
    }

    // Sayfa yüklendiğinde sepet sayacını güncelle
    sepetSayaciniGuncelle();

    // Miktar değişikliği
    $(document).on('change', '.miktar-input', function () {
        var urun_id = $(this).data('id');
        var yeni_miktar = $(this).val();

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                urun_id: urun_id,
                miktar: yeni_miktar
            },
            success: function () {
                location.reload();
            }
        });
    });
});