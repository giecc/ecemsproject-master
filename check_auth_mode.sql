-- Check current authentication mode
SELECT SERVERPROPERTY('IsIntegratedSecurityOnly') as [Authentication_Mode];
-- Returns 0 if SQL Server and Windows Authentication mode (Mixed Mode)
-- Returns 1 if Windows Authentication only

-- Enable Mixed Mode Authentication
EXEC xp_instance_regwrite 
    N'HKEY_LOCAL_MACHINE', 
    N'Software\Microsoft\MSSQLServer\MSSQLServer',
    N'LoginMode',
    REG_DWORD,
    2;

-- Recreate login with a different password (sometimes helps)
IF EXISTS (SELECT * FROM sys.server_principals WHERE name = 'Bitirme_Projesi')
BEGIN
    DROP LOGIN [Bitirme_Projesi]
END
GO

CREATE LOGIN [Bitirme_Projesi] 
WITH PASSWORD = N'Bitirme123!', -- Using a stronger password
DEFAULT_DATABASE = [EWAHandmade],
CHECK_EXPIRATION = OFF,
CHECK_POLICY = OFF
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

-- Grant necessary permissions
EXEC sp_addrolemember 'db_owner', 'Bitirme_Projesi'
GO 