<?php
include_once "./../DB/db_access.php";

/**
 * Deletes a post from the database.
 *
 * @param int $id The ID of the post to delete.
 *
 * @return bool True if the deletion was successful, false otherwise.
 */
function deletePost($id) {
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
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

    try {
        if (deletePost($id)) {
            http_response_code(200);
            echo "Post deleted successfully";
        } else {
            http_response_code(500);
            echo "Error deleting post";
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}