USE [EWAHandmade]
GO

SELECT 
    c.name as ColumnName,
    t.name as DataType,
    c.is_nullable as IsNullable
FROM sys.columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
WHERE object_id = OBJECT_ID('Kullanicilar')
ORDER BY c.column_id;
GO 