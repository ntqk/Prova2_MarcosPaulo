<?php

// Nome: Marcos Paulo da Silva

// Configurações para a conexão com o banco de dados

// $host: Define o endereço do servidor onde o banco de dados está hospedado.
// 'localhost' significa que o banco de dados está rodando na mesma máquina
// que o servidor web (onde o PHP está sendo executado).
$host = 'localhost';

// $dbname: Define o nome do banco de dados ao qual queremos nos conectar.
// Neste caso, o banco de dados se chama 'senai_login'.
$dbname = 'senai_login';

// $user: Define o nome de usuário para acessar o banco de dados.
// 'root' é um usuário comum em ambientes de desenvolvimento MySQL,
// geralmente com permissões totais (mas não recomendado para produção sem senha!).
$user = 'root';

// $pass: Define a senha para o usuário do banco de dados.
// '' (uma string vazia) significa que não há senha configurada para o usuário 'root'.
// Isso é comum em configurações locais de desenvolvimento, mas **altamente inseguro** para servidores de produção.
$pass = 'root';

$port = '3307'; // Altere conforme sua instalação do MySQL/MariaDB
$charset = 'utf8mb4'; // Essencial para acentuação e compatibilidade

// O bloco try...catch é usado para tratamento de erros.
// Se algo der errado ao tentar conectar ao banco de dados (dentro do 'try'),
// o código dentro do 'catch' será executado para lidar com o erro de forma controlada.
try {
    // Tenta criar uma nova conexão com o banco de dados usando PDO.
    // Adicionando charset ao DSN para evitar problemas com acentuação!
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);

    // Configura o PDO para lançar exceções em caso de erros.
    // PDO::ATTR_ERRMODE: Define o modo de relatório de erros.
    // PDO::ERRMODE_EXCEPTION: Se ocorrer um erro na comunicação com o banco (ex: consulta SQL errada),
    // o PDO lançará uma exceção (um tipo especial de erro que pode ser "capturado" pelo bloco catch).
    // Isso é bom para depuração e para tratar erros de forma mais robusta.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: define o modo de fetch padrão para associativo (não obrigatório, mas recomendado)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Se a conexão for bem-sucedida, a variável $pdo agora contém um objeto
    // que representa a conexão com o banco de dados. Este objeto $pdo
    // será usado em outros scripts (como o login.php) para executar consultas SQL.

} catch (PDOException $e) {
    // Se ocorrer qualquer erro (uma PDOException) durante a tentativa de conexão no bloco 'try',
    // o código dentro deste bloco 'catch' será executado.

    // $e é um objeto que contém informações sobre o erro que ocorreu.
    // $e->getMessage() retorna uma mensagem descrevendo o erro.

    // NÃO exponha detalhes do erro em produção!
    // Em ambiente de desenvolvimento, pode mostrar. Em produção, grave em log.
    die("Erro de conexão com o banco de dados. Verifique as configurações ou contate o suporte. Erro técnico: " . htmlspecialchars($e->getMessage()));
}

// Se o script chegar até aqui sem entrar no 'catch', significa que a conexão
// foi estabelecida com sucesso e o objeto $pdo está pronto para ser usado.
// Se este arquivo for incluído (usando 'require' ou 'include') em outros scripts PHP,
// a variável $pdo estará disponível para eles.
?>