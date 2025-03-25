<?php
// Incluindo o autoload do Composer para usar o PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

/**
 * Função para enviar e-mail de alerta para múltiplos destinatários
 * @param string $assunto - O assunto do e-mail
 * @param string $corpoEmail - O conteúdo do e-mail
 * @return bool - Retorna true se o e-mail foi enviado com sucesso, false em caso de erro
 */
function enviarEmail($assunto, $corpoEmail) {
    // Instancia o objeto PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();  // Definir o envio usando SMTP
        $mail->Host = 'smtps2.webmail.rj.gov.br';  // Servidor SMTP (exemplo: Webmail RJ)
        $mail->SMTPAuth = true;  // Ativar autenticação SMTP
        $mail->Username = 'impressora@central.rj.gov.br';  // Seu e-mail de envio
        $mail->Password = 'central@123';  // Sua senha de e-mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Definir criptografia TLS
        $mail->Port = 465;  // Porta do servidor SMTP (pode variar dependendo do servidor)

        // Definir o remetente
        $mail->setFrom('impressora@central.rj.gov.br', 'Notificações de Estoque');  // Remetente

        // Adicionando múltiplos destinatários
        $receiverEmails = [
            'grodrigues@central.rj.gov.br',
            'alexandrerocha@central.rj.gov.br',
            'impressora@central.rj.gov.br',
            'maikalves@central.rj.gov.br'
        ];

        foreach ($receiverEmails as $email) {
            $mail->addAddress($email);  // Adiciona o destinatário ao e-mail
        }

        // Conteúdo do e-mail
        $mail->isHTML(true);  // Definir o e-mail como HTML
        $mail->Subject = $assunto;  // Assunto do e-mail
        $mail->Body    = $corpoEmail;  // Corpo do e-mail (em HTML)

        // Enviar o e-mail
        $mail->send();
        return true;  // Retorna true se o e-mail foi enviado com sucesso
    } catch (Exception $e) {
        // Se ocorrer algum erro, exibe a mensagem de erro
        echo "A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}";
        return false;  // Retorna false em caso de erro
    }
}
?>
