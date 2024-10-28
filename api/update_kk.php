<?php
header('Content-Type: application/json');

// Include the connection file
require '../helper/connection.php';

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['code_id']) && isset($input['kk_name'])) {
    $codeId = $input['code_id'];
    $kkName = $input['kk_name'];

    try {
        // Get database connection
        $pdo = getDatabaseConnection();

        // Check if CODE_ID already exists
        $stmt = $pdo->prepare('SELECT * FROM master_kk WHERE CODE_ID = ?');
        $stmt->execute([$codeId]);
        
        if ($stmt->rowCount() > 0) {
            // If exists, perform update
            $stmt = $pdo->prepare('UPDATE master_kk SET KK_NAME = ? WHERE CODE_ID = ?');
            $stmt->execute([$kkName, $codeId]);
            echo json_encode(['status' => 'success', 'message' => 'Data updated successfully.']);
        } else {
            // If not, perform insert
            $stmt = $pdo->prepare('INSERT INTO master_kk (CODE_ID, KK_NAME) VALUES (?, ?)');
            $stmt->execute([$codeId, $kkName]);
            echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
?>
