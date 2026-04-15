<?php
// api/update_profile_photo.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['foto']['error'] ?? 'No file';
    $msg = 'Erro no upload: ' . $err;
    if ($err === UPLOAD_ERR_INI_SIZE)
        $msg = 'O arquivo excede o limite definido no servidor (php.ini: upload_max_filesize).';
    if ($err === UPLOAD_ERR_FORM_SIZE)
        $msg = 'O arquivo excede o limite definido no formulário.';
    if ($err === UPLOAD_ERR_PARTIAL)
        $msg = 'O upload foi feito apenas parcialmente.';
    if ($err === UPLOAD_ERR_NO_FILE)
        $msg = 'Nenhum arquivo foi enviado.';
    json_response(['success' => false, 'message' => $msg], 400);
}

$file = $_FILES['foto'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    json_response(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG ou GIF.'], 400);
}

// Check size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    json_response(['success' => false, 'message' => 'A imagem é muito grande. Tamanho máximo: 5MB.'], 400);
}

$upload_dir = __DIR__ . '/../assets/uploads/perfil/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
$target_path = $upload_dir . $filename;
$web_path = 'assets/uploads/perfil/' . $filename;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    try {
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET foto = ? WHERE id = ?");
        $stmt->execute([$web_path, $user_id]);

        // Update session if needed
        $_SESSION['user_foto'] = $web_path;

        json_response(['success' => true, 'message' => 'Foto atualizada!', 'path' => $web_path]);
    }
    catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Erro ao salvar no banco.'], 500);
    }
}
else {
    json_response(['success' => false, 'message' => 'Erro ao mover o arquivo.'], 500);
}
?>
