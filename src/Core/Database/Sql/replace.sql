/*
Retornar Clientes OU Representantes com base em seu Documento [CPF ou CNPJ]
Requires: [documento]
*/

SELECT 
    `cod`, 

FROM 

    `?_users_dados` 
    
WHERE

    (
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`cpf`, ' ', ''), '-', ''), '\', ''), '/', ''), '.', '')  = %s_documento OR 
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`cnpj`, ' ', ''), '-', ''), '\', ''), '/', ''), '.', '')  = %s_documento
    ) 