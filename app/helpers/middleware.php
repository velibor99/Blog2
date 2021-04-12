<?php

function blocedUser($redirect = '/index.php')
{
    if ($loggedUserData['is_banned']= 1) {
        $_SESSION['message'] = 'You are blocked from commenting for 24h becaouse of youre ofensive words';
        $_SESSION['type'] = 'error';
        header('location: ' . BASE_URL . $redirect);
        exit(0);


    }
}

function usersOnly($redirect = '/login.php')
{
    if (empty($_SESSION['id'])) {
        $_SESSION['message'] = 'You need to login first';
        $_SESSION['type'] = 'error';
        header('location: ' . BASE_URL . $redirect);
        exit(0);


    }
}

function adminOnly($redirect = '/index.php')
{
    if (empty($_SESSION['id']) || empty($_SESSION['admin'])) {
        $_SESSION['message'] = 'You are not authorized';
        $_SESSION['type'] = 'error';
        header('location: ' . BASE_URL . $redirect);
        exit(0);
    }
}

function guestsOnly($redirect = '/index.php')
{
    if (isset($_SESSION['id'])) {
        header('location: ' . BASE_URL . $redirect);
        exit(0);
    }    
}