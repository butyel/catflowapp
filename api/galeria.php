<?php
// api/galeria.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $gato_id = (int)($_GET['gato_id'] ?? 0);
        if (!$gato_id)
            json_response(['success' => false, 'message' => 'ID do gato obrigatório'], 400);

        $stmt = $pdo->prepare("SELECT * FROM galeria WHERE gato_id = ? ORDER BY data DESC, created_at DESC");
        $stmt->execute([$gato_id]);
        json_response(['success' => true, 'data' => $stmt->fetchAll()]);
    }
    elseif ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $gato_id = (int)($_POST['gato_id'] ?? 0);
        $legenda = sanitize_input($_POST['legenda'] ?? '');
        $data_foto = $_POST['data'] ?? date('Y-m-d');

        if (!$gato_id)
            json_response(['success' => false, 'message' => 'ID do gato obrigatório'], 400);

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $upload_dir = __DIR__ . '/../assets/uploads/galeria/';
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'galeria_' . time() . '_' . uniqid() . '.' . $ext;
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $foto_path = 'assets/uploads/galeria/' . $filename;
                $stmt = $pdo->prepare("INSERT INTO galeria (gato_id, foto_path, legenda, data) VALUES (?, ?, ?, ?)");
                $stmt->execute([$gato_id, $foto_path, $legenda, $data_foto]);
                json_response(['success' => true, 'message' => 'Foto adicionada à galeria!']);
            }
        }
        json_response(['success' => false, 'message' => 'Falha no upload da foto'], 400);
    }
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], 500);
}
