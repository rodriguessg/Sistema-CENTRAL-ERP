<?php
// Lista de IPs a serem testados
$ips = [
    '10.9.5.1',
    '10.9.5.48',
    '10.9.5.49',
    '10.11.92.131',
    '10.11.92.132',
    '10.11.92.6',
    '10.11.92.4',
    '10.9.5.142' // IP da impressora
];

// Simulação de tempo offline (em minutos, armazenado em array estático para exemplo)
$offline_times = [];
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['offline_times'])) {
    $_SESSION['offline_times'] = [];
    foreach ($ips as $ip) {
        $_SESSION['offline_times'][$ip] = 0; // Inicializa com 0 minutos
    }
}

// Função para realizar o ping e atualizar o tempo offline
function ping($ip, &$offline_times) {
    $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
        ? "ping -n 1 -w 1000 $ip" 
        : "ping -c 1 -W 1 $ip";
    
    exec($command, $output, $status);
    
    global $_SESSION;
    if ($status !== 0) {
        if (!isset($offline_times[$ip])) $offline_times[$ip] = 0;
        $offline_times[$ip] += 1;
        $_SESSION['offline_times'][$ip] = $offline_times[$ip];
    } else {
        $offline_times[$ip] = 0;
        $_SESSION['offline_times'][$ip] = 0;
    }
    
    return $status === 0 ? true : false;
}

// Função para verificar o status da impressora usando SNMP
function checkPrinterStatus($ip) {
    if (!ping($ip, $_SESSION['offline_times'])) {
        return "Com Falha"; // Se não responde ao ping, assume falha
    }

    // Verifica se a função snmpget está disponível
    if (!function_exists('snmpget')) {
        return "Disponível (SNMP não disponível)"; // Fallback se SNMP não estiver habilitado
    }

    // Configuração SNMP
    $community = "public";
    $hrPrinterStatus_oid = ".1.3.6.1.2.1.25.3.5.1.1.1"; // OID padrão para status da impressora (HOST-RESOURCES-MIB::hrPrinterStatus)
    $prtMarkerStatus_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.1"; // OID para status do marcador (Printer-MIB, pode indicar atividade de impressão)

    // Tenta obter o status da impressora (hrPrinterStatus)
    $hr_status = @snmpget($ip, $community, $hrPrinterStatus_oid);
    if ($hr_status !== false) {
        $hr_value = trim(preg_replace('/.*: /', '', $hr_status)); // Extrai o valor numérico ou string
        switch ($hr_value) {
            case '3': // idle
                return "Disponível";
            case '4': // printing
                return "Imprimindo";
            case '5': // warmup
                return "Aquecendo";
            default:
                return "Com Falha"; // other, unknown, etc.
        }
    }

    // Alternativa: Status do marcador
    $marker_status = @snmpget($ip, $community, $prtMarkerStatus_oid);
    if ($marker_status !== false) {
        $marker_value = trim(preg_replace('/.*: /', '', $marker_status));
        if (stripos($marker_value, "printing") !== false || $marker_value == '4') {
            return "Imprimindo";
        } else if ($marker_value == '3') {
            return "Disponível";
        }
    }

    return "Com Falha"; // Se falhar as consultas
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento TIC - Status de Rede</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .online {
            color: green;
            font-weight: bold;
        }
        .offline {
            color: red;
            font-weight: bold;
        }
        .printing {
            color: #FFA500; /* Laranja para imprimindo */
            font-weight: bold;
        }
        .available {
            color: green;
            font-weight: bold;
        }
        .failed {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Gerenciamento TIC - Monitoramento de Rede</h1>
    <table>
        <tr>
            <th>Endereço IP</th>
            <th>Status</th>
            <th>Tempo Offline (min)</th>
            <th>Status Específico (Impressora)</th>
        </tr>
        <?php foreach ($ips as $ip): 
            $is_online = ping($ip, $_SESSION['offline_times']);
            $printer_status = ($ip === '10.9.5.142') ? checkPrinterStatus($ip) : '';
            $status_class = $is_online ? 'online' : 'offline';
            $printer_class = '';
            if ($ip === '10.9.5.142') {
                $printer_class = strtolower(str_replace(' ', '', str_replace('(', '', str_replace(')', '', $printer_status))));
            }
        ?>
            <tr>
                <td><?php echo htmlspecialchars($ip); ?></td>
                <td class="<?php echo $status_class; ?>">
                    <?php echo $is_online ? 'Online' : 'Offline'; ?>
                </td>
                <td>
                    <?php echo $_SESSION['offline_times'][$ip]; ?> min
                </td>
                <td class="<?php echo $printer_class; ?>">
                    <?php echo ($ip === '10.9.5.142') ? $printer_status : '-'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>