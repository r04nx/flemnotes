<?php
require_once 'config.php';
require_once 'database.php';

class API {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        try {
            switch ($action) {
                case 'get':
                    $this->getNote();
                    break;
                case 'save':
                    $this->saveNote();
                    break;
                case 'upload':
                    $this->uploadFile();
                    break;
                case 'delete-file':
                    $this->deleteFile();
                    break;
                case 'verify-password':
                    $this->verifyPassword();
                    break;
                default:
                    $this->sendError('Invalid action', 400);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function getNote() {
        $route = $_GET['route'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($route)) {
            $this->sendError('Route is required', 400);
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM notes WHERE route = ?');
        $stmt->execute([$route]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note) {
            // Return empty note for new routes
            $this->sendSuccess([
                'exists' => false,
                'content' => '',
                'is_public' => 1,
                'files' => [],
                'statistics' => [
                    'characters' => 0,
                    'words' => 0,
                    'files' => 0
                ]
            ]);
            return;
        }

        // Check if password protected
        if (!empty($note['password_hash'])) {
            if (empty($password)) {
                $this->sendSuccess([
                    'exists' => true,
                    'password_protected' => true,
                    'requires_password' => true
                ]);
                return;
            }

            // Verify password
            $passwordMatch = password_verify($password, $note['password_hash']);
            
            // Debug logging (remove in production)
            error_log("Password verification for route '{$route}': " . ($passwordMatch ? 'SUCCESS' : 'FAILED'));
            error_log("Password length: " . strlen($password));
            
            if (!$passwordMatch) {
                $this->sendError('Invalid password', 403);
                return;
            }
        }

        // Get files
        $stmt = $this->db->prepare('SELECT * FROM files WHERE note_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$note['id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content = $note['content'] ?? '';
        $this->sendSuccess([
            'exists' => true,
            'content' => $content,
            'is_public' => (int)$note['is_public'],
            'password_protected' => !empty($note['password_hash']),
            'files' => $files,
            'statistics' => [
                'characters' => mb_strlen($content),
                'words' => str_word_count($content),
                'files' => count($files)
            ]
        ]);
    }

    private function saveNote() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $route = $input['route'] ?? '';
        $content = $input['content'] ?? '';
        $password = $input['password'] ?? '';
        $isPublic = $input['is_public'] ?? 1;
        $currentPassword = $input['current_password'] ?? '';
        $removePassword = $input['remove_password'] ?? false;

        if (empty($route)) {
            $this->sendError('Route is required', 400);
            return;
        }

        // Validate route (alphanumeric only)
        if (!preg_match('/^[a-zA-Z0-9]+$/', $route)) {
            $this->sendError('Route must be alphanumeric', 400);
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM notes WHERE route = ?');
        $stmt->execute([$route]);
        $existingNote = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check password for existing protected notes
        if ($existingNote && !empty($existingNote['password_hash'])) {
            // If note is password protected, verify current password
            if (empty($currentPassword) || !password_verify($currentPassword, $existingNote['password_hash'])) {
                $this->sendError('Invalid password', 403);
                return;
            }
        }

        $timestamp = time();

        if ($existingNote) {
            // Update existing note
            $sql = 'UPDATE notes SET content = ?, is_public = ?, updated_at = ?';
            $params = [$content, $isPublic, $timestamp];
            
            // Handle password update/removal
            if ($removePassword) {
                // Explicitly remove password
                $sql .= ', password_hash = NULL';
            } elseif (!empty($password)) {
                // User wants to set/change password
                $sql .= ', password_hash = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            // If neither removePassword nor password is set, keep existing password
            
            $sql .= ' WHERE route = ?';
            $params[] = $route;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $noteId = $existingNote['id'];
        } else {
            // Create new note
            $passwordHash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
            $stmt = $this->db->prepare('INSERT INTO notes (route, content, password_hash, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$route, $content, $passwordHash, $isPublic, $timestamp, $timestamp]);
            $noteId = $this->db->lastInsertId();
        }

        $this->sendSuccess([
            'message' => 'Note saved successfully',
            'note_id' => $noteId
        ]);
    }

    private function uploadFile() {
        $route = $_POST['route'] ?? '';
        $currentPassword = $_POST['password'] ?? '';

        if (empty($route)) {
            $this->sendError('Route is required', 400);
            return;
        }

        if (!isset($_FILES['file'])) {
            $this->sendError('No file uploaded', 400);
            return;
        }

        $file = $_FILES['file'];

        // Better error handling for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = 'File upload error';
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage = 'File is too large. Maximum size is 50MB';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = 'File was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = 'No file was uploaded';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMessage = 'Missing temporary folder';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage = 'Failed to write file to disk';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMessage = 'File upload stopped by extension';
                    break;
            }
            $this->sendError($errorMessage, 400);
            return;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $maxSizeMB = MAX_FILE_SIZE / (1024 * 1024);
            $this->sendError('File too large. Maximum size is ' . $maxSizeMB . 'MB', 400);
            return;
        }

        if ($file['size'] === 0) {
            $this->sendError('File is empty', 400);
            return;
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($extension, BLOCKED_EXTENSIONS)) {
            $this->sendError('File type not allowed for security reasons', 403);
            return;
        }

        // Get or create note
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE route = ?');
        $stmt->execute([$route]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note) {
            // Create note if doesn't exist
            $timestamp = time();
            $stmt = $this->db->prepare('INSERT INTO notes (route, content, created_at, updated_at) VALUES (?, ?, ?, ?)');
            $stmt->execute([$route, '', $timestamp, $timestamp]);
            $noteId = $this->db->lastInsertId();
        } else {
            // Check password if protected
            if (!empty($note['password_hash'])) {
                if (!password_verify($currentPassword, $note['password_hash'])) {
                    $this->sendError('Invalid password', 403);
                    return;
                }
            }
            $noteId = $note['id'];
        }

        // Generate unique filename with hash
        $hash = substr(md5(uniqid() . $file['name'] . time()), 0, 8);
        $storedName = pathinfo($file['name'], PATHINFO_FILENAME) . '_' . $hash . '.' . $extension;
        $destination = UPLOAD_DIR . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->sendError('Failed to save file', 500);
            return;
        }

        // Save file info to database
        $stmt = $this->db->prepare('INSERT INTO files (note_id, original_name, stored_name, file_size, mime_type, uploaded_at) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $noteId,
            $file['name'],
            $storedName,
            $file['size'],
            $file['type'],
            time()
        ]);

        $fileId = $this->db->lastInsertId();

        $this->sendSuccess([
            'message' => 'File uploaded successfully',
            'file' => [
                'id' => $fileId,
                'original_name' => $file['name'],
                'stored_name' => $storedName,
                'file_size' => $file['size'],
                'mime_type' => $file['type']
            ]
        ]);
    }

    private function deleteFile() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $fileId = $input['file_id'] ?? 0;
        $password = $input['password'] ?? '';

        $stmt = $this->db->prepare('SELECT f.*, n.password_hash FROM files f JOIN notes n ON f.note_id = n.id WHERE f.id = ?');
        $stmt->execute([$fileId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            $this->sendError('File not found', 404);
            return;
        }

        // Check password if protected
        if (!empty($file['password_hash'])) {
            if (!password_verify($password, $file['password_hash'])) {
                $this->sendError('Invalid password', 403);
                return;
            }
        }

        // Delete physical file
        $filePath = UPLOAD_DIR . $file['stored_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $stmt = $this->db->prepare('DELETE FROM files WHERE id = ?');
        $stmt->execute([$fileId]);

        $this->sendSuccess(['message' => 'File deleted successfully']);
    }

    private function verifyPassword() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $route = $input['route'] ?? '';
        $password = $input['password'] ?? '';

        $stmt = $this->db->prepare('SELECT password_hash FROM notes WHERE route = ?');
        $stmt->execute([$route]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note || empty($note['password_hash'])) {
            $this->sendSuccess(['valid' => true]);
            return;
        }

        $valid = password_verify($password, $note['password_hash']);
        $this->sendSuccess(['valid' => $valid]);
    }

    private function sendSuccess($data) {
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}

// Handle API requests
if (isset($_GET['action'])) {
    $api = new API();
    $api->handleRequest();
}
