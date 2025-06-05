<?php
function getPaginatedData($pdo, $sqlBase, $searchFields, $searchTerm, $orderBy, $limit, $page) {
    $offset = ($page - 1) * $limit;
    $where = '';
    $params = [];

    // Bangun klausa pencarian
    if ($searchTerm && $searchFields) {
        $like = [];
        foreach ($searchFields as $field) {
            $like[] = "$field LIKE :search";
        }
        $where = "WHERE " . implode(" OR ", $like);
        $params['search'] = "%$searchTerm%";
    }

    // Total data
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $sqlBase $where");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    // Ambil data
    $stmt = $pdo->prepare("SELECT * FROM $sqlBase $where ORDER BY $orderBy LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'data' => $data,
        'total' => $total,
        'pages' => $totalPages,
        'current' => $page,
        'search' => $searchTerm
    ];
}
?>