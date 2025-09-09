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

    // Configuração SNMP baseada na imagem
    $community = "public"; // Confirmado na configuração da impressora
    $general_status_oid = "1"; // Status geral do dispositivo
    $job_status_oid = ".1"; // Status do trabalho atual
    $printer_status_oid = "1"; // Status da bandeja

    // Tenta obter o status geral
    $general_status = @snmpget($ip, $community, $general_status_oid);
    if ($general_status === false) {
        return "Com Falha";
    }

    // Tenta obter o status do trabalho atual
    $job_status = @snmpget($ip, $community, $job_status_oid);
    if ($job_status !== false) {
        $job_value = trim($job_status);
        if (stripos($job_value, "printing") !== false || stripos($job_value, "processing") !== false) {
            return "Imprimindo";
        }
    }

    // Verifica status da bandeja como alternativa
    $printer_status = @snmpget($ip, $community, $printer_status_oid);
    if ($printer_status !== false) {
        $printer_value = trim($printer_status);
        if (stripos($printer_value, "printing") !== false) {
            return "Imprimindo";
        }
    }

    // Interpretação do status geral
    $status_value = trim($general_status);
    if (stripos($status_value, "running") !== false) {
        return "Disponível";
    } else {
        return "Com Falha";
    }
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