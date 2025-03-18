<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar o autoload do Composer
require 'vendor/autoload.php';

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $data_g = $_POST['data_g'];
    $email = $_POST['email'];

    $sql = "INSERT INTO agendamentos (nome, descricao, data_g, email) VALUES ('$nome', '$descricao', '$data_g', '$email')";

    if ($conn->query($sql) === TRUE) {
        // Enviar e-mail com PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'gabrielzsouzarodrigues@gmail.com'; // Seu e-mail
            $mail->Password = 'yprucjchuemzwzkb'; // Senha de aplicativo gerada no Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatário
            $mail->setFrom('no-reply@asscon.com', 'Equipe Asscon');
            $mail->addAddress($email, $nome);

            // Assunto e corpo
            $mail->isHTML(true);
            $mail->Subject = "Lembrete de Agendamento - $nome";
            $mail->Body    = "Olá, $nome.<br><br>Aqui está o seu agendamento:<br><br>Descrição: $descricao<br>Data: $data_g<br><br>Atenciosamente,<br>Equipe Asscon";

            $mail->send();
            echo "Agendamento salvo com sucesso e lembrete enviado!";
             // Redirecionar para a página de sucesso
        header('Location: mensagem.php?mensagem=sucesso3&pagina=contrato.php');
        exit();
        } catch (Exception $e) {
            echo "Erro ao enviar o lembrete por e-mail: {$mail->ErrorInfo}";
        }
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
