<?php

include "functions.php";



function StartApp()
{
    $content = '';
    session_start();

    $user = new Users(USER, PASS, CONNETIONSTRING);

    $user->GetUserRole($_COOKIE['user']);
    setcookie("user-id", $user->GetCurrentUserIdQuery($_COOKIE['user']), time()+3600);



    //check if user wants to logout
    LogOutUserAndRedirectToHome();

    if(isset($_GET['linqSearch']))
    {
        $_SESSION['linqSearch'] = $_GET['linqSearch'];

        header("Location: https://backlinqs.com/link-browser");
        exit();
    }


    if($_SERVER['REQUEST_URI'] == '/login')
    {
        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];

        $isUserLoggedIn = VerifyLoginPage();

        if($isUserLoggedIn == true)
        {
            setcookie("status", "true");
            setcookie("user", $_SESSION['inputEmail']);
            header("Location: https://backlinqs.com/dashboard");
            exit();
        }
    }

    if($_SERVER['REQUEST_URI'] == '/new-user')
    {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];
        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];


    }

    if($_SERVER['REQUEST_URI'] == '/dashboard/account')
    {
        $_SESSION['company'] = $_POST['company'];
        $_SESSION['facebook'] = $_POST['facebook'];
        $_SESSION['youtube'] = $_POST['youtube'];
        $_SESSION['instagram'] = $_POST['instagram'];
        $_SESSION['phone'] = $_POST['phone'];
        $_SESSION['about'] = $_POST['about'];

        UpdateUserInfo($_COOKIE['user-id']);
    }




    //gets a specific page/linq content and information
    if(strpos($_SERVER['REQUEST_URI'], 'linq-browser/') == true)
    {
        $permalink = str_replace("/linq-browser/", "", $_SERVER['REQUEST_URI']);
        $content = GetCurrentLinkPageContentWithPermalink($permalink);

        //$content = html_entity_decode($content);
    }
    elseif (strpos($_SERVER['REQUEST_URI'], 'dashboard/') == true)
    {

        $permalink = str_replace("/dashboard/", "", $_SERVER['REQUEST_URI']);

        $content = GetCurrentPageContentWithPermalink($permalink);

        $dashboardPages = array("Create Post");

        $checkArray = in_array($content[0]['Title'], $dashboardPages);
        if($checkArray == true && $user->role > 4)
        {
            header("Location: https://backlinqs.com/dashboard");
            exit();
        }

    }
    else {

        $permalink = str_replace("/", "", $_SERVER['REQUEST_URI']);


        //Gets the content of the current page from the database
        $content = GetCurrentPageContentWithPermalink($permalink);

        $dashboardPages = array("Account", "Linqs Provided", "Linqs Requested", "Profile", "Post", "Create Post", "Create User", "Insert Scripts");

        $checkArray = in_array($content[0]['Title'], $dashboardPages);

        if($checkArray == true)
        {
            header("Location: https://backlinqs.com/dashboard");
            exit();
        }
        //$content = html_entity_decode($content);
    }


    //Constructs the page
    $page = new PageConstruct($content);



    CreatePostPage();
    CreateNewLInkPage();


    CreateNewUserPage($page);


    //returns a view of the page
    return $page;
}

function LogOutUserAndRedirectToHome()
{
    if($_GET['logout'] == 'true')
    {
        LogOutUserQuery($_COOKIE['user']);
        unset($_COOKIE['user']);
        unset($_COOKIE['status']);

        $homeUrl = "https://{$_SERVER['HTTP_HOST']}";
        header('Location: https://backlinqs.com');
        exit();
    }
}

function CreatePostPage()
{

    if($_POST['title'] != null && $_POST['content'] != null && strpos($_SERVER['REQUEST_URI'], 'create-post') == true)
    {
        if(CheckUserStatus($_COOKIE['user']) == true)
        {
            $userId = GetCurrentUserIdQuery($_COOKIE['user']);

            //Check current users post to make sure two pages are not created with the same title
            CheckCurrentUserPostTitlesAndInsert($userId, $_POST['title'], $_POST['content'], $_POST['type']);

        }
        else
        {
            ?>
            <script>
                alert("You must be logged in to submit a post");
                window.location.replace("/");
            </script>
            <?php
        }
    }
}

function CreateNewLInkPage()
{

    if($_POST['linkTitle'] != null && $_POST['linkDescription'] != null && $_POST['linkUrl'] != null )
    {

        if(CheckUserStatus($_COOKIE['user']) == true)
        {
            $userId = GetCurrentUserIdQuery($_COOKIE['user']);

            //Check current users post to make sure two pages are not created with the same title
            CheckCurrentUserLinkTitlesAndInsert($userId,  $_POST['linkUrl'],  $_POST['linkDescription'], $_POST['linkTitle']);


        }
        else
        {
            ?>
            <script>
                alert("You must be logged in to submit a post");
                window.location.replace("/");
            </script>
            <?php
        }
    }
}

function CreateNewUserPage($page)
{
    if($page->pageTitle == $page->newUserPageTitle)
    {


        $firstName = $_SESSION['firstName'];
        $lastName = $_SESSION['lastName'];
        $email = $_SESSION['inputEmail'];
        $pass = $_SESSION['inputPassword'];


        if($firstName != NULL && $lastName != NULL && $email != NULL && $pass != NULL)
        {
            CheckForUniqueUserUponCreation($firstName, $lastName, $email, $pass);

        }

    }
}

function VerifyLoginPage()
{

    $email = $_SESSION['inputEmail'];
    $pass = $_SESSION['inputPassword'];


    if($email != null && $pass != null)
    {

        $isLoggedIn = VerifyUser($email, $pass);


        if($isLoggedIn == true)
        {
            return true;

        }
        else
        {
            return false;
        }
    }
}