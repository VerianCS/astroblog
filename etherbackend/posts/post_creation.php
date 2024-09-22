<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "./../DB/db_access.php";

/**
 * Creates a new post in the database.
 *
 * @param string $title   The title of the post.
 * @param string $content The content of the post.
 *
 * @return string A success message or an error message.
 */
function createPost($title, $content) {
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);

    try {
        // Execute the statement and check for success
        if ($stmt->execute()) {
            return "New post created successfully.";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    } finally {
        $stmt->close();
        $conn->close();
    }
}

// Get the posted data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
    exit;
}

$title = filter_input(INPUT_POST, 'postTitle', FILTER_SANITIZE_STRING);
$content = filter_input(INPUT_POST, 'postContent', FILTER_SANITIZE_STRING);

if (empty($title) || empty($content)) {
    http_response_code(400);
    echo json_encode(array("message" => "Title and content are required."));
    exit;
}

try {
    $result = createPost($title, $content);
    http_response_code(200);
    echo json_encode(array("message" => $result));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "An error occurred: " . $e->getMessage()));
}