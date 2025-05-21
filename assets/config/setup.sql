USE master;
GO

IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'EWAHandmade')
BEGIN
    CREATE DATABASE EWAHandmade;
END
GO

USE EWAHandmade;
GO

CREATE TABLE Kullanicilar (
    KullaniciID INT IDENTITY(1,1) PRIMARY KEY,
    Ad NVARCHAR(50) NOT NULL,
    Soyad NVARCHAR(50) NOT NULL,
    Email NVARCHAR(100) NOT NULL UNIQUE,
    Sifre NVARCHAR(255) NOT NULL,
    KayitTarihi DATETIME DEFAULT GETDATE(),
    Aktif BIT DEFAULT 1
);
GO

CREATE TABLE Urunler (
    UrunID INT IDENTITY(1,1) PRIMARY KEY,
    UrunAdi NVARCHAR(100) NOT NULL,
    Aciklama NVARCHAR(MAX),
    Kategori NVARCHAR(50) NOT NULL,
    Fiyat DECIMAL(10,2) NOT NULL,
    ResimURL NVARCHAR(255),
    Stok INT NOT NULL DEFAULT 0,
    Aktif BIT DEFAULT 1,
    EklenmeTarihi DATETIME DEFAULT GETDATE()
);
GO

CREATE TABLE Sepet (
    SepetID INT IDENTITY(1,1) PRIMARY KEY,
    KullaniciID INT NOT NULL,
    UrunID INT NOT NULL,
    Adet INT NOT NULL DEFAULT 1,
    EklenmeTarihi DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_Sepet_Kullanici FOREIGN KEY (KullaniciID) 
        REFERENCES Kullanicilar(KullaniciID),
    CONSTRAINT FK_Sepet_Urun FOREIGN KEY (UrunID) 
        REFERENCES Urunler(UrunID)
);
GO

CREATE TABLE Siparisler (
    SiparisID INT IDENTITY(1,1) PRIMARY KEY,
    KullaniciID INT NOT NULL,
    SiparisTarihi DATETIME DEFAULT GETDATE(),
    ToplamTutar DECIMAL(10,2) NOT NULL,
    SiparisDurumu NVARCHAR(20) DEFAULT 'Beklemede',
    CONSTRAINT FK_Siparisler_Kullanici FOREIGN KEY (KullaniciID) 
        REFERENCES Kullanicilar(KullaniciID)
);
GO

CREATE TABLE SiparisDetaylari (
    SiparisDetayID INT IDENTITY(1,1) PRIMARY KEY,
    SiparisID INT NOT NULL,
    UrunID INT NOT NULL,
    Adet INT NOT NULL,
    BirimFiyat DECIMAL(10,2) NOT NULL,
    CONSTRAINT FK_SiparisDetaylari_Siparis FOREIGN KEY (SiparisID) 
        REFERENCES Siparisler(SiparisID),
    CONSTRAINT FK_SiparisDetaylari_Urun FOREIGN KEY (UrunID) 
        REFERENCES Urunler(UrunID)
);
GO

-- Create login
IF NOT EXISTS (SELECT * FROM sys.server_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    CREATE LOGIN [Bitirme_Projesi] WITH PASSWORD = '12345';
END
GO

-- Create database user
IF NOT EXISTS (SELECT * FROM sys.database_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    CREATE USER [Bitirme_Projesi] FOR LOGIN [Bitirme_Projesi];
    ALTER ROLE db_owner ADD MEMBER [Bitirme_Projesi];
END
GO 