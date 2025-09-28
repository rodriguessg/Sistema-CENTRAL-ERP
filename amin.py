import pandas as pd

# Ler o arquivo Excel
df = pd.read_excel('Pasta4.xlsx', sheet_name='Pasta3')

# Função para formatar valores para SQL
def format_value(value, column):
    if pd.isna(value):
        return 'NULL'
    if isinstance(value, str):
        if column == 'hora' and ':' in value:  # Para valores de hora como "08:00"
            return f"'{value}'"
        return f"'{value}'"
    if column == 'hora':  # Para valores decimais de hora
        return f"SEC_TO_TIME({value} * 24 * 3600)"
    if column == 'data':  # Para valores de data
        return f"DATE_ADD('1900-01-01', INTERVAL ({value} - 2) DAY)"
    return str(value)

# Iniciar o comando SQL
sql = "INSERT INTO VIAGENS (id, bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, grat_pcd_idoso, moradores, passageiros, tipo_viagem, data, created_at, subida_id) VALUES\n"

# Adicionar cada linha ao comando SQL
values = []
for _, row in df.iterrows():
    values.append(f"({row['ID']}, {format_value(row['Bonde'], 'bonde')}, {format_value(row['Saida'], 'saida')}, {format_value(row['Retorno'], 'retorno')}, {format_value(row['Maquinista'], 'maquinista')}, {format_value(row['Agente'], 'agente')}, {format_value(row['Hora'], 'hora')}, {row['Pagantes']}, {row['gratuidade']}, {row['grat_pcd_idoso']}, {row['moradores']}, {row['Passageiros']}, {format_value(row['TIPO_VIAGEM'], 'tipo_viagem')}, {format_value(row['Data'], 'data')}, NULL, {format_value(row['subida_id'], 'subida_id')})")
sql += ",\n".join(values) + ";"

# Salvar o comando SQL em um arquivo
with open('insert_viagens.sql', 'w') as f:
    f.write(sql)

print("Comando SQL gerado e salvo em 'insert_viagens.sql'")