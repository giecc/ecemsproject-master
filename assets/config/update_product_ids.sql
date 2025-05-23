USE [EWAHandmade]
GO

-- Önce geçici bir tablo oluştur
CREATE TABLE #TempUrunler (
    EskiID INT,
    YeniID NVARCHAR(10),
    UrunAdi NVARCHAR(100),
    Kategori NVARCHAR(50)
);

-- Mevcut ürünleri geçici tabloya kopyala
INSERT INTO #TempUrunler (EskiID, UrunAdi, Kategori)
SELECT UrunID, UrunAdi, Kategori FROM Urunler;

-- Yeni ID'leri ata
UPDATE #TempUrunler
SET YeniID = CASE 
    WHEN Kategori = 'Panço' THEN 'P' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Elbise' THEN 'E' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Sal' THEN 'S' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Yöresel Dokuma' THEN 'YD' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Fular' THEN 'F' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Tunik' THEN 'T' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Otantik Yelek' THEN 'OY' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Bolero' THEN 'PE' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    WHEN Kategori = 'Pestemel' THEN 'B' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
    ELSE 'D' + RIGHT('000' + CAST(ROW_NUMBER() OVER (PARTITION BY Kategori ORDER BY EskiID) AS VARCHAR(3)), 3)
END;

-- Yeni bir tablo oluştur
CREATE TABLE UrunlerYeni (
    UrunID NVARCHAR(10) PRIMARY KEY,
    UrunAdi NVARCHAR(100) NOT NULL,
    Aciklama NVARCHAR(MAX),
    Kategori NVARCHAR(50) NOT NULL,
    Fiyat DECIMAL(10,2) NOT NULL,
    ResimURL NVARCHAR(255),
    Stok INT NOT NULL DEFAULT 0,
    Aktif BIT DEFAULT 1,
    EklenmeTarihi DATETIME DEFAULT GETDATE()
);

-- Verileri yeni tabloya aktar
INSERT INTO UrunlerYeni (UrunID, UrunAdi, Aciklama, Kategori, Fiyat, ResimURL, Stok, Aktif, EklenmeTarihi)
SELECT 
    t.YeniID,
    u.UrunAdi,
    u.Aciklama,
    u.Kategori,
    u.Fiyat,
    u.ResimURL,
    u.Stok,
    u.Aktif,
    u.EklenmeTarihi
FROM Urunler u
JOIN #TempUrunler t ON u.UrunID = t.EskiID;

-- Eski tabloyu sil ve yeni tabloyu yeniden adlandır
DROP TABLE Urunler;
EXEC sp_rename 'UrunlerYeni', 'Urunler';

-- Geçici tabloyu temizle
DROP TABLE #TempUrunler;
GO 