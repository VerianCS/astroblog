<?php
include_once "./../DB/db_access.php";

/**
 * Edits a post in the database.
 *
 * @param int $id The ID of the post to edit.
 * @param string $title The new title of the post.
 * @param string $content The new content of the post.
 *
 * @return bool True if the edit was successful, false otherwise.
 */
function editPost($id, $title, $content) {
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
    $conn->close();
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    try {
        if (editPost($id, $title, $content)) {
            http_response_code(200);
            echo "Post edited successfully";
        } else {
            http_response_code(500);
            echo "Error editing post";
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}