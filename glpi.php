<!DOCTYPE html>
     <html lang="pt-BR">
     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <meta http-equiv="X-Frame-Options" content="sameorigin">
         <title>Helpdesk Login</title>
         <style>
             body {
                 margin: 0;
                 padding: 0;
                 display: flex;
                 justify-content: center;
                 align-items: center;
                 height: 100vh;
                 background-color: #f0f0f0;
             }
             iframe {
                 border: none;
                 box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                 width: 100%;
                 height: 600px;
             }
         </style>
     </head>
     <body>
           <iframe src='http://helpdesk.intranet.central.rj.gov.br/front/central.php?embed&dashboard=assistance&entities_id=0&is_recursive=0&token=27a5c20d-fb4a-54a8-a064-c4bc9ea79a7c' frameborder='0' width='800' height='600' allowtransparency>

         <script>
             document.getElementById('helpdeskIframe').onload = function() {
                 console.log('Iframe iniciado: http://helpdesk.intranet.central.rj.gov.br');
                 setTimeout(function() {
                     try {
                         const iframe = document.getElementById('helpdeskIframe');
                         const message = {
                             login: 'kkk',
                             password: 'central@123',
                             action: 'fillLoginForm'
                         };
                         iframe.contentWindow.postMessage(message, 'http://helpdesk.intranet.central.rj.gov.br');
                         console.log('Mensagem enviada ao iframe:', message);
                     } catch (e) {
                         console.error('Erro ao enviar mensagem ao iframe:', e.message, e.stack);
                     }
                 }, 2000); // Atraso de 2 segundos
             };

             // Escutar respostas do iframe (se aplicável)
             window.addEventListener('message', function(event) {
                 if (event.origin !== 'http://helpdesk.intranet.central.rj.gov.br') {
                     console.warn('Mensagem de origem não permitida:', event.origin);
                     return;
                 }
                 console.log('Resposta do iframe:', event.data);
             });

             document.getElementById('helpdeskIframe').onerror = function() {
                 console.error('Erro ao carregar o iframe. Verifique a URL ou a conectividade.');
             };
         </script>
     </body>
     </html>