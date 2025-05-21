USE [EWAHandmade]
GO

-- Add SonGiris column if it doesn't exist
IF NOT EXISTS (
    SELECT * FROM sys.columns 
    WHERE object_id = OBJECT_ID(N'[dbo].[Kullanicilar]') 
    AND name = 'SonGiris'
)
BEGIN
    ALTER TABLE [dbo].[Kullanicilar]
    ADD SonGiris DATETIME NULL
END
GO 