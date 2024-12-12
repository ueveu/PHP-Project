<?php
/**
 * Validation helper functions
 * Contains functions for form validation and error handling
 */

/**
 * Validate username
 * @param string $username Username to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateUsername($username) {
    $username = trim($username);
    
    if (empty($username)) {
        return ['valid' => false, 'message' => 'Benutzername ist erforderlich.'];
    }
    
    if (strlen($username) < 3) {
        return ['valid' => false, 'message' => 'Benutzername muss mindestens 3 Zeichen lang sein.'];
    }
    
    if (strlen($username) > 50) {
        return ['valid' => false, 'message' => 'Benutzername darf maximal 50 Zeichen lang sein.'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        return ['valid' => false, 'message' => 'Benutzername darf nur Buchstaben, Zahlen, Unterstriche und Bindestriche enthalten.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate password
 * @param string $password Password to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePassword($password) {
    if (empty($password)) {
        return ['valid' => false, 'message' => 'Passwort ist erforderlich.'];
    }
    
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Passwort muss mindestens 6 Zeichen lang sein.'];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'message' => 'Passwort muss mindestens einen Großbuchstaben enthalten.'];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'message' => 'Passwort muss mindestens einen Kleinbuchstaben enthalten.'];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Passwort muss mindestens eine Zahl enthalten.'];
    }
    
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Passwort muss mindestens ein Sonderzeichen enthalten.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate post title
 * @param string $title Title to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePostTitle($title) {
    $title = trim($title);
    
    if (empty($title)) {
        return ['valid' => false, 'message' => 'Titel ist erforderlich.'];
    }
    
    if (strlen($title) < 3) {
        return ['valid' => false, 'message' => 'Titel muss mindestens 3 Zeichen lang sein.'];
    }
    
    if (strlen($title) > 255) {
        return ['valid' => false, 'message' => 'Titel darf maximal 255 Zeichen lang sein.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate post content
 * @param string $content Content to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePostContent($content) {
    $content = trim($content);
    
    if (empty($content)) {
        return ['valid' => false, 'message' => 'Inhalt ist erforderlich.'];
    }
    
    if (strlen($content) < 10) {
        return ['valid' => false, 'message' => 'Inhalt muss mindestens 10 Zeichen lang sein.'];
    }
    
    if (strlen($content) > 50000) {
        return ['valid' => false, 'message' => 'Inhalt darf maximal 50.000 Zeichen lang sein.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate contact form message
 * @param string $message Message to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateContactMessage($message) {
    $message = trim($message);
    
    if (empty($message)) {
        return ['valid' => false, 'message' => 'Nachricht ist erforderlich.'];
    }
    
    if (strlen($message) < 10) {
        return ['valid' => false, 'message' => 'Nachricht muss mindestens 10 Zeichen lang sein.'];
    }
    
    if (strlen($message) > 1000) {
        return ['valid' => false, 'message' => 'Nachricht darf maximal 1.000 Zeichen lang sein.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate firstname
 * @param string $firstname Firstname to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateFirstname($firstname) {
    $firstname = trim($firstname);
    
    if (empty($firstname)) {
        return ['valid' => false, 'message' => 'Vorname ist erforderlich.'];
    }
    
    if (strlen($firstname) < 3) {
        return ['valid' => false, 'message' => 'Vorname muss mindestens 3 Zeichen lang sein.'];
    }
    
    if (strlen($firstname) > 20) {
        return ['valid' => false, 'message' => 'Vorname darf maximal 20 Zeichen lang sein.'];
    }
    
    if (!preg_match('/^[a-zA-ZäöüÄÖÜß-]+$/', $firstname)) {
        return ['valid' => false, 'message' => 'Vorname darf nur Buchstaben und Bindestriche enthalten (keine Leerzeichen).'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate lastname
 * @param string $lastname Lastname to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateLastname($lastname) {
    $lastname = trim($lastname);
    
    if (empty($lastname)) {
        return ['valid' => false, 'message' => 'Nachname ist erforderlich.'];
    }
    
    if (strlen($lastname) < 3) {
        return ['valid' => false, 'message' => 'Nachname muss mindestens 3 Zeichen lang sein.'];
    }
    
    if (strlen($lastname) > 20) {
        return ['valid' => false, 'message' => 'Nachname darf maximal 20 Zeichen lang sein.'];
    }
    
    if (!preg_match('/^[a-zA-ZäöüÄÖÜß\s-]+$/', $lastname)) {
        return ['valid' => false, 'message' => 'Nachname darf nur Buchstaben, Leerzeichen und Bindestriche enthalten.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validate alias
 * @param string $alias Alias to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateAlias($alias) {
    $alias = trim($alias);
    
    if (empty($alias)) {
        return ['valid' => false, 'message' => 'Alias ist erforderlich.'];
    }
    
    if (strlen($alias) < 4) {
        return ['valid' => false, 'message' => 'Alias muss mindestens 4 Zeichen lang sein.'];
    }
    
    if (strlen($alias) > 8) {
        return ['valid' => false, 'message' => 'Alias darf maximal 8 Zeichen lang sein.'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $alias)) {
        return ['valid' => false, 'message' => 'Alias darf nur Buchstaben, Zahlen, Unterstriche und Bindestriche enthalten.'];
    }
    
    return ['valid' => true, 'message' => ''];
} 