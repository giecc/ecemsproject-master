-- First verify authentication mode is correct
SELECT SERVERPROPERTY('IsIntegratedSecurityOnly') as [Authentication_Mode];
-- Should return 0 now after changing to Mixed Mode

-- Recreate login with a simpler password
IF EXISTS (SELECT * FROM sys.server_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    DROP LOGIN [Bitirme_Projesi]
END
GO

CREATE LOGIN [Bitirme_Projesi] 
WITH PASSWORD = N'12345', -- Back to original simple password
DEFAULT_DATABASE = [EWAHandmade],
CHECK_EXPIRATION = OFF,
CHECK_POLICY = OFF
GO

-- Enable the login explicitly
ALTER LOGIN [Bitirme_Projesi] ENABLE
GO

USE [EWAHandmade]
GO

IF EXISTS (SELECT * FROM sys.database_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    DROP USER [Bitirme_Projesi]
END
GO

CREATE USER [Bitirme_Projesi] FOR LOGIN [Bitirme_Projesi]
GO

-- Grant permissions
EXEC sp_addrolemember 'db_owner', 'Bitirme_Projesi'
GO

-- Verify login exists
SELECT name, is_disabled, type_desc 
FROM sys.server_principals 
WHERE name = 'Bitirme_Projesi';

-- Verify database user exists
SELECT name, type_desc 
FROM sys.database_principals 
WHERE name = 'Bitirme_Projesi'; 