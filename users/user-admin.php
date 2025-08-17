<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $action = $_POST['action'];

    if ($action === 'add_user') {
        $stmt = $conn->prepare("SELECT username FROM nwengine_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($existing_username);

        if ($stmt->fetch()) {
            $message = "Username " . $username . " already exists";
        } else {
            $stmt->free_result();
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO nwengine_user (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $message = "User " . $username . " added successfully";
        }
        $stmt->close();
    } elseif ($action === 'update_password') {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE nwengine_user SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $password, $username);
        $stmt->execute();
        $message = "Password for user " . $username . " updated successfully";
        $stmt->close();
    } elseif ($action === 'remove_user') {
        if($username == 'admin') {
            $message = "cannot remove admin user";
            //$stmt->close();
        } else {
        $stmt = $conn->prepare("DELETE FROM nwengine_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $message = "User " . $username . " removed successfully";
        $stmt->close();

        }
    }

    $_SESSION['message'] = $message;
    $_SESSION['action'] = $action;
}