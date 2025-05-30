-- Drop the existing login if it exists
IF EXISTS (SELECT * FROM sys.server_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    DROP LOGIN [Bitirme_Projesi]
END
GO

-- Create new login with proper settings
CREATE LOGIN [Bitirme_Projesi] 
WITH PASSWORD = '12345',
DEFAULT_DATABASE = [EWAHandmade],
CHECK_EXPIRATION = OFF,
CHECK_POLICY = OFF
GO

-- Grant server access
GRANT CONNECT SQL TO [Bitirme_Projesi]
GO

USE [EWAHandmade]
GO

-- Drop user if exists
IF EXISTS (SELECT * FROM sys.database_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    DROP USER [Bitirme_Projesi]
END
GO

-- Create database user
CREATE USER [Bitirme_Projesi] FOR LOGIN [Bitirme_Projesi]
GO

-- Add user to database role
EXEC sp_addrolemember 'db_owner', 'Bitirme_Projesi'
GO 