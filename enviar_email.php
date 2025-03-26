<?php
// Incluindo o autoload do Composer para usar o PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

/**
 * Função para enviar e-mail de alerta para múltiplos destinatários
 * @param string $assunto - O assunto do e-mail
 * @param string $corpoEmail - O conteúdo do e-mail
 * @param array $emailDestinatarios - Um array com os e-mails dos destinatários
 * @return bool - Retorna true se o e-mail foi enviado com sucesso, false em caso de erro
 */
function enviarEmail($assunto, $corpoEmail, $emailDestinatarios) {
    // Instancia o objeto PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();  // Definir o envio usando SMTP
        $mail->Host = 'smtps2.webmail.rj.gov.br';  // Servidor SMTP (Webmail RJ)
        $mail->SMTPAuth = true;  // Ativar autenticação SMTP
        $mail->Username = 'impressora@central.rj.gov.br';  // E-mail de envio
        $mail->Password = 'central@123';  // Senha de e-mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Criptografia TLS
        $mail->Port = 465;  // Porta do servidor SMTP

        // Definir o remetente
        $mail->setFrom('impressora@central.rj.gov.br', 'Notificações de Estoque');

        // Adicionando os destinatários
        foreach ($emailDestinatarios as $emailDestinatario) {
            $mail->addAddress($emailDestinatario);  // Adiciona cada destinatário ao e-mail
        }

        // Conteúdo do e-mail
        $mail->isHTML(true);  // Definir o e-mail como HTML
        $mail->Subject = $assunto;  // Assunto do e-mail
        $mail->Body    = $corpoEmail;  // Corpo do e-mail (HTML)

        // Enviar o e-mail
        if ($mail->send()) {
            return true;  // Retorna true se o e-mail foi enviado com sucesso
        } else {
            return false;  // Caso contrário, retorna false
        }
    } catch (Exception $e) {
        // Se ocorrer erro, exibe a mensagem de erro
        echo "A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}";
        return false;  // Retorna false em caso de erro
    }
}
?>
