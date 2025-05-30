USE [EWAHandmade]
GO

-- Get all users with their information
SELECT 
    KullaniciID,
    Ad,
    Soyad,
    Email,
    SonGiris,
    Aktif
FROM dbo.Kullanicilar
ORDER BY KullaniciID; 