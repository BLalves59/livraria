<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';

try {

    // GET - Buscar livro por ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if (isset($_GET['id'])) {

            $id = (int) $_GET['id'];

            $sql = "SELECT * FROM livros WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $livro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$livro) {
                http_response_code(404);

                echo json_encode([
                    'erro' => 'Livro não encontrado.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            echo json_encode($livro, JSON_UNESCAPED_UNICODE);

        } else {

            $sql = "SELECT * FROM livros";
            $stmt = $pdo->query($sql);

            $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($livros, JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    // POST - Cadastrar livro
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $dados = json_decode(file_get_contents('php://input'), true);

        if (!$dados) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'Dados inválidos.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $sql = "INSERT INTO livros
                (titulo, autor, categoria, preco, estoque, capa)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $dados['titulo'],
            $dados['autor'],
            $dados['categoria'],
            $dados['preco'],
            $dados['estoque'],
            $dados['capa'] ?? null
        ]);

        $id = $pdo->lastInsertId();

        http_response_code(201);

        echo json_encode([
            'mensagem' => 'Livro cadastrado com sucesso.',
            'id' => (int) $id
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // PUT - Atualizar livro
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (!isset($_GET['id'])) {
        http_response_code(400);

        echo json_encode([
            'erro' => 'Informe o ID do livro.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $id = (int) $_GET['id'];

    $dados = json_decode(file_get_contents('php://input'), true);

    if (!$dados) {
        http_response_code(400);

        echo json_encode([
            'erro' => 'Dados inválidos.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Verifica se o livro existe
    $stmt = $pdo->prepare("SELECT id FROM livros WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        http_response_code(404);

        echo json_encode([
            'erro' => 'Livro não encontrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $sql = "UPDATE livros SET
                titulo = ?,
                autor = ?,
                categoria = ?,
                preco = ?,
                estoque = ?,
                capa = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $dados['titulo'],
        $dados['autor'],
        $dados['categoria'],
        $dados['preco'],
        $dados['estoque'],
        $dados['capa'] ?? null,
        $id
    ]);

    echo json_encode([
        'mensagem' => 'Livro atualizado com sucesso.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
    }

    // DELETE - Excluir livro
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (!isset($_GET['id'])) {
        http_response_code(400);

        echo json_encode([
            'erro' => 'Informe o ID do livro.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $id = (int) $_GET['id'];

    // Verifica se o livro existe
    $stmt = $pdo->prepare("SELECT id FROM livros WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        http_response_code(404);

        echo json_encode([
            'erro' => 'Livro não encontrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM livros WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'mensagem' => 'Livro excluído com sucesso.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
    }

    // Método não permitido
    http_response_code(405);

    echo json_encode([
        'erro' => 'Método não permitido.'
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'erro' => 'Erro ao acessar o banco de dados.'
    ], JSON_UNESCAPED_UNICODE);
}