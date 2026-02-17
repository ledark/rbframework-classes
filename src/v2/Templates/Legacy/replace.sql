#tabela, campo, valoratual, novovalor
UPDATE {tabela} SET {campo} = REPLACE({campo}, '{valoratual}', '{novovalor}') WHERE {campo} LIKE '%{valoratual}%'