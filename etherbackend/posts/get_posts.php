<?php
include_once "./../DB/db_access.php";

/**
 * Retrieves all posts from the database in ascending order by ID.
 *
 * @return array An array of post objects.
 */

 header('Content-Type: application/json');

function getPosts() {
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = array();
    while ($row = $result->fetch_assoc()) {
        $posts[] = array(
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content']
        );
    }

    $stmt->close();
    $conn->close();

    return $posts;
}

try {
    $posts = getPosts();
    header('Content-Type: application/json');
    echo json_encode($posts);
} catch (Exception $e) {
    http_response_code(500);
    echo "An error occurred: " . $e->getMessage();
}