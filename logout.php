<?php
/**
 * Logout page
 * Handles user logout
 */

require_once 'includes/config.php';
require_once 'includes/user_functions.php';

$result = logoutUser();
showSuccess($result['message']);
header('Location: index.php');
exit;
?> 