const ilSelect = document.getElementById('il');
const ilceSelect = document.getElementById('ilce');

function fillIlSelect(selectedIl = "") {
    ilSelect.innerHTML = '<option value="">İl seçiniz</option>';
    for (const il in turkiyeAdres) {
        const option = document.createElement('option');
        option.value = il;
        option.textContent = il;
        if (il === selectedIl) option.selected = true;
        ilSelect.appendChild(option);
    }
}

function fillIlceSelect(selectedIl, selectedIlce = "") {
    ilceSelect.innerHTML = '<option value="">İlçe seçiniz</option>';
    if (selectedIl && turkiyeAdres[selectedIl]) {
        for (const ilce in turkiyeAdres[selectedIl]) {
            const option = document.createElement('option');
            option.value = ilce;
            option.textContent = ilce;
            if (ilce === selectedIlce) option.selected = true;
            ilceSelect.appendChild(option);
        }
    }
}

// Sayfa açıldığında il ve ilçe select'lerini doldur
if (ilSelect && ilceSelect) {
    fillIlSelect();
    fillIlceSelect(""); // Boş il ile başlat
    ilSelect.addEventListener('change', function() {
        fillIlceSelect(this.value);
    });
}

// Düzenle fonksiyonu için global erişim
window.fillIlSelect = fillIlSelect;
window.fillIlceSelect = fillIlceSelect;