const express = require('express');
const sql = require('mssql');
const app = express();
const port = 3000;

// SQL Server bağlantı ayarları
const config = {
    user: 'Bitirme_Projesi',
    password: '12345',
    server: 'localhost',  // SQL Server'ın çalıştığı bilgisayar
    database: 'EWAHandmade',
    options: {
        trustServerCertificate: true  // Sertifika hatası için
    }
};

// Ana sayfa ("/") için route
app.get('/', (req, res) => {
    res.send('Merhaba! API başarıyla çalışıyor.');
});

// Kullanıcılar endpoint'i
app.get('/kullanicilar', async (req, res) => {
    try {
        await sql.connect(config);  // Veritabanına bağlan
        const result = await sql.query('SELECT * FROM kullanicilar');  // Veritabanı sorgusu
        res.json(result.recordset);  // Veritabanından gelen veriyi JSON olarak döndür
    } catch (err) {
        console.error(err);
        res.status(500).send('Veritabanı hatası');
    }
});

// Sunucuyu başlat
app.listen(port, () => {
    console.log(`Sunucu http://localhost:${port} adresinde çalışıyor`);
});
