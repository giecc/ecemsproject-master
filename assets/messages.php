<?php
function displayMessages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">';
        echo htmlspecialchars($_SESSION['success_message']);
        echo '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-error">';
        echo htmlspecialchars($_SESSION['error_message']);
        echo '</div>';
        unset($_SESSION['error_message']);
    }
}
?> 