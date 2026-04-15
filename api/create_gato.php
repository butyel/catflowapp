<?php
require_once __DIR__ . '/base.php';

rateLimit('create_gato', 20);
requireAuth();

$user_id = get_logged_user_id();
$nome = trim($_POST['nome'] ?? '');

if (empty($nome)) {
    ApiResponse::errorJson('Nome do gato é obrigatório', 400);
}

if (strlen($nome) > 100) {
    ApiResponse::errorJson('Nome deve ter no máximo 100 caracteres', 400);
}

$sexo = $_POST['sexo'] ?? 'M';
if (!in_array($sexo, ['M', 'F'])) {
    ApiResponse::errorJson('Sexo inválido', 400);
}

$status = $_POST['status'] ?? 'ativo';
$valid_statuses = ['ativo', 'tratamento', 'Doado', 'aposentado', 'óbito'];
if (!in_array($status, $valid_statuses)) {
    ApiResponse::errorJson('Status inválido', 400);
}

$castrado = !empty($_POST['castrado']) ? 1 : 0;
$idade = !empty($_POST['idade']) ? Security::sanitizeInput($_POST['idade']) : null;
$peso = !empty($_POST['peso']) ? (float)$_POST['peso'] : null;
$raca = !empty($_POST['raca']) ? Security::sanitizeInput($_POST['raca']) : null;
$data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
$cor_padrao = !empty($_POST['cor_padrao']) ? Security::sanitizeInput($_POST['cor_padrao']) : null;
$microchip = !empty($_POST['microchip']) ? Security::sanitizeInput($_POST['microchip']) : null;
$pedigree = !empty($_POST['pedigree']) ? Security::sanitizeInput($_POST['pedigree']) : null;
$ala_id = !empty($_POST['ala_id']) ? (int)$_POST['ala_id'] : null;
$doencas_pre_existentes = !empty($_POST['doencas_pre_existentes']) ? Security::sanitizeInput($_POST['doencas_pre_existentes']) : null;
$historico = !empty($_POST['historico']) ? Security::sanitizeInput($_POST['historico']) : null;
$foto_path = null;

if ($data_nascimento && !Security::validateDate($data_nascimento)) {
    ApiResponse::errorJson('Data de nascimento inválida', 400);
}

if ($peso !== null && ($peso < 0 || $peso > 50)) {
    ApiResponse::errorJson('Peso inválido', 400);
}

if ($ala_id) {
    $checkAla = $pdo->prepare("SELECT id FROM alas WHERE id = ? AND user_id = ?");
    $checkAla->execute([$ala_id, $user_id]);
    if (!$checkAla->fetch()) {
        ApiResponse::errorJson('Ala não encontrada', 400);
    }
}

if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto_file'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        ApiResponse::errorJson('Tipo de imagem inválido. Use JPG, PNG, WEBP ou GIF.', 400);
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        ApiResponse::errorJson('Imagem muito grande. Máximo 5MB.', 400);
    }
    
    $upload_dir = __DIR__ . '/../assets/uploads/gatos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'gato_' . time() . '_' . uniqid() . '.' . $ext;
    $target_file = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $foto_path = 'assets/uploads/gatos/' . $filename;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO gatos (user_id, nome, foto, sexo, idade, castrado, peso, status, raca, data_nascimento, cor_padrao, microchip, pedigree, ala_id, doencas_pre_existentes, historico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $nome, $foto_path, $sexo, $idade, $castrado, $peso, $status, $raca, $data_nascimento, $cor_padrao, $microchip, $pedigree, $ala_id, $doencas_pre_existentes, $historico]);
    
    ApiResponse::successJson(['id' => $pdo->lastInsertId()], 'Gato cadastrado com sucesso!');
} catch (Exception $e) {
    ApiResponse::errorJson('Erro interno ao salvar', 500);
}
