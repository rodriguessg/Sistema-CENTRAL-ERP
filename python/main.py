import pandas as pd
import os
from datetime import datetime, timedelta

# Caminho do arquivo
file_path = r'C:\Users\gabri\Pasta1.xlsx'

# Verificar se o arquivo existe
if not os.path.exists(file_path):
    print(f"Erro: O arquivo {file_path} não foi encontrado. Verifique se o arquivo está no diretório correto.")
    exit()

try:
    # Ler o arquivo Excel
    df = pd.read_excel(file_path, sheet_name='Plan1')
except PermissionError:
    print(f"Erro: Permissão negada para acessar {file_path}. Certifique-se de que o arquivo não está aberto em outro programa (ex.: Excel) e que você tem permissões de leitura.")
    exit()
except FileNotFoundError:
    print(f"Erro: O arquivo {file_path} não foi encontrado. Verifique o caminho e o nome do arquivo.")
    exit()
except Exception as e:
    print(f"Erro ao ler o arquivo Excel: {e}")
    exit()

# Tamanho do lote
batch_size = 200

# Arquivo de log para diagnóstico
log_path = r'C:\xampp\htdocs\Sistema-CENTRAL-ERP\viagens_log.txt'
log_messages = []

# Gerar arquivos SQL em lotes
for i in range(0, len(df), batch_size):
    batch = df[i:i+batch_size]
    sql = "INSERT IGNORE INTO VIAGENS (id, bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, grat_pcd_idoso, moradores, passageiros, tipo_viagem, data, created_at, subida_id) VALUES\n"
    values = []
    for _, row in batch.iterrows():
        # Tratar created_at como NULL se estiver vazio
        created_at = 'NULL' if pd.isna(row['created_at']) else f"'{row['created_at']}'"
        # Tratar hora: converter decimal para TIME, manter string HH:MM, ou NULL para NaN
        if pd.isna(row['Hora']):
            hora = 'NULL'
            log_messages.append(f"ID {row['ID']}: Hora é NULL")
        elif isinstance(row['Hora'], float):
            seconds = row['Hora'] * 86400
            hora = f"'{timedelta(seconds=int(seconds))}'"
        else:
            hora_str = str(row['Hora']).strip()
            hora = f"'{hora_str}:00'" if len(hora_str.split(':')) == 2 else f"'{hora_str}'"
        # Converter colunas numéricas para inteiro
        id_value = int(row['ID']) if pd.notna(row['ID']) else row['ID']
        # Tratar subida_id: usar 0 se vazio (teste para evitar 'pendente')
        subida_id = int(row['subida_id']) if pd.notna(row['subida_id']) else 0
        if pd.isna(row['subida_id']):
            log_messages.append(f"ID {id_value}: subida_id definido como 0, tipo_viagem={row['TIPO_VIAGEM']}, retorno={row['Retorno']}")
        grat_pcd_idoso = int(row['grat_pcd_idoso']) if pd.notna(row['grat_pcd_idoso']) else 0
        pagantes = int(row['Pagantes']) if pd.notna(row['Pagantes']) else 0
        gratuidade = int(row['gratuidade']) if pd.notna(row['gratuidade']) else 0
        moradores = int(row['moradores']) if pd.notna(row['moradores']) else 0
        passageiros = int(row['Passageiros']) if pd.notna(row['Passageiros']) else 0
        # Tratar Data: converter para 'YYYY-MM-DD', usar '1970-01-01' para inválidos
        if pd.isna(row['Data']):
            data = "'1970-01-01'"
            log_messages.append(f"ID {id_value}: Data é NULL, usando '1970-01-01'")
        elif isinstance(row['Data'], pd.Timestamp):
            data = f"'{row['Data'].strftime('%Y-%m-%d')}'"
        elif isinstance(row['Data'], (int, float)):
            try:
                data = f"'{(datetime(1899, 12, 30) + timedelta(days=row['Data'])).strftime('%Y-%m-%d')}'"
            except (ValueError, OverflowError):
                data = "'1970-01-01'"
                log_messages.append(f"ID {id_value}: Data inválida (serial: {row['Data']}), usando '1970-01-01'")
        else:
            try:
                parsed_date = pd.to_datetime(row['Data'], errors='coerce', dayfirst=True)
                data = f"'{parsed_date.strftime('%Y-%m-%d')}'" if not pd.isna(parsed_date) else "'1970-01-01'"
                if pd.isna(parsed_date):
                    log_messages.append(f"ID {id_value}: Data inválida (string: {row['Data']}), usando '1970-01-01'")
            except (ValueError, TypeError):
                data = "'1970-01-01'"
                log_messages.append(f"ID {id_value}: Data inválida (string: {row['Data']}), usando '1970-01-01'")
        # Escapar aspas simples nos campos de texto
        bonde = str(row['Bonde']).replace("'", "''")
        saida = str(row['Saida']).replace("'", "''")
        retorno = str(row['Retorno']).replace("'", "''")
        if not retorno:
            log_messages.append(f"ID {id_value}: Retorno está vazio")
        maquinista = str(row['Maquinista']).replace("'", "''")
        agente = str(row['Agente']).replace("'", "''")
        tipo_viagem = str(row['TIPO_VIAGEM']).replace("'", "''")
        # Adicionar linha ao comando SQL
        values.append(f"({id_value}, '{bonde}', '{saida}', '{retorno}', '{maquinista}', '{agente}', {hora}, {pagantes}, {gratuidade}, {grat_pcd_idoso}, {moradores}, {passageiros}, '{tipo_viagem}', {data}, {created_at}, {subida_id})")
    sql += ",\n".join(values) + ";"
    output_path = r'C:\xampp\htdocs\Sistema-CENTRAL-ERP\insert_viagens_batch_{}.sql'.format(i // batch_size + 1)
    try:
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(sql)
        print(f"Lote {i // batch_size + 1} salvo em: {output_path}")
    except PermissionError:
        print(f"Erro: Permissão negada para salvar em {output_path}. Tente salvar em outro diretório ou execute como administrador.")
    except Exception as e:
        print(f"Erro ao salvar o arquivo SQL: {e}")

# Salvar log de diagnóstico
try:
    with open(log_path, 'w', encoding='utf-8') as f:
        f.write("\n".join(log_messages))
    print(f"Log de diagnóstico salvo em: {log_path}")
except Exception as e:
    print(f"Erro ao salvar o log: {e}")