<?php
if (function_exists('snmpget')) {
    $status = snmpget('10.9.5.142', 'public', '.1.3.6.1.2.1.25.3.2.1.5.1');
    echo "Status Geral: " . $status . "\n";
    $job = snmpget('10.9.5.142', 'public', '.1.3.6.1.2.1.43.10.1.1.9.1.1');
    echo "Status Job: " . $job . "\n";
} else {
    echo "SNMP não disponível";
}
?>