<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID

    // // Database connection (replace with your credentials)
    // $conn = new mysqli("localhost", "root", "", "hhh");

    // if ($conn->connect_error) {
    //     echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    //     exit;
    // }

    // Fetch hosteler details by ID
    $query = $conn->prepare("SELECT * FROM hostelers WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'date_of_birth' => $data['date_of_birth'],
            'status' => $data['status'],
            'created_at' => $data['created_at']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No hosteler found']);
    }

    $query->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID parameter missing']);
}
?>
