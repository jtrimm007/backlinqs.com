<?php

include "functions.php";



function StartApp()
{
    $content = '';
    session_start();

    if($_COOKIE['status'] == 'true')
    {
        $user = new Users(USER, PASS, CONNETIONSTRING);

        $user->GetUserRole($_COOKIE['user']);
        $user->SetCurrentUserIdQuery($_COOKIE['user']);

    }


    if(isset($_GET['fbclid']))
    {
        header("Location: https://backlinqs.com");
        exit();
    }

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

    if($_SERVER['REQUEST_URI'] == '/verify-account' || $_SERVER['REQUEST_URI'] == '/new-user')
    {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];
        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];
        $_SESSION['confirmPassword'] = $_POST['confirmPassword'];
    }

    if($_SERVER['REQUEST_URI'] == '/backlink-qualifier' || $_SERVER['REQUEST_URI'] == '/verify-account')
    {
        $_SESSION['website'] = $_POST['website'];
        $_SESSION['inputEmail'] = $_POST['email'];
        $_SESSION['gb'] = $_POST['gb'];
        $_SESSION['bl'] = $_POST['bl'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];
        $_SESSION['confirmPassword'] = $_POST['confirmPassword'];

    }

    if($_SERVER['REQUEST_URI'] == '/dashboard/profile')
    {
        $_SESSION['company'] = $_POST['company'];
        $_SESSION['website'] = $_POST['website'];
        $_SESSION['facebook'] = $_POST['facebook'];
        $_SESSION['youtube'] = $_POST['youtube'];
        $_SESSION['instagram'] = $_POST['instagram'];
        $_SESSION['phone'] = $_POST['phone'];
        $_SESSION['about'] = $_POST['about'];

        UpdateUserInfo($_COOKIE['user-id']);
    }

    if($_SERVER['REQUEST_URI'] == '/dashboard/account')
    {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];


        //UpdateUserInfo($_COOKIE['user-id']);
    }




    //gets a specific page/linq content and information
    if(strpos($_SERVER['REQUEST_URI'], 'linq-browser/') == true)
    {
        $permalink = str_replace("/linq-browser/", "", $_SERVER['REQUEST_URI']);
        $content = GetCurrentLinkPageContentWithPermalink($permalink);

        if($content == null)
        {

            $url = str_replace("/linq-browser/", "", $_SERVER['REQUEST_URI']);

            $search = new GoogleSearch();

            $urlInfo = $search->GetUrlInformationNoneArray($url);

            $selectEmail = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

            $email = $selectEmail->SelectEmailFromScrap($url);

            $pulledEmail = '';
           foreach($email as $each)
           {
              //var_dump($each);
               $pulledEmail = $each["email"];
           }
            $content = array(
                    array(
                            "Title" => $urlInfo[0],
                        "LinkUrl" => $urlInfo[2],
                        "Content" => $urlInfo[1]

                    ),
                $pulledEmail
            );
        }
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
    elseif(strpos($_SERVER['REQUEST_URI'], 'blog/') == true)
    {
        $permalink = str_replace("/blog/", "", $_SERVER['REQUEST_URI']);



        $content = GetCurrentPageContentWithPermalink($permalink);
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


    CreateNewUserPage();


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


    if($_POST['title'] != null && $_POST['content'] != null && strpos($_SERVER['REQUEST_URI'], 'create-post') !== false)
    {
        if(CheckUserStatus($_COOKIE['user']) == true)
        {


            //Check current users post to make sure two pages are not created with the same title
            CheckCurrentUserPostTitlesAndInsert($_COOKIE['user-id'], $_POST['title'], $_POST['content'], $_POST['type']);

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
            //Check current users post to make sure two pages are not created with the same title
            CheckCurrentUserLinkTitlesAndInsert($_COOKIE['user-id'],  $_POST['linkUrl'],  $_POST['linkDescription'], $_POST['linkTitle']);
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

function CreateNewUserPage()
{

    if($_SERVER['REQUEST_URI'] == "/new-user" || $_SERVER['REQUEST_URI'] == "/verify-account" || $_SERVER['REQUEST_URI'] == "/backlink-qualifier")
    {
        $firstName = $_SESSION['firstName'];
        $lastName = $_SESSION['lastName'];
        $email = $_SESSION['inputEmail'];
        $pass = $_SESSION['inputPassword'];
        $website = $_SESSION['website'];
        $guestBlogger = $_SESSION['gb'];
        $linkExchanger = $_SESSION['bl'];


        if($firstName != NULL && $lastName != NULL && $email != NULL && $pass != NULL)
        {
            $_SESSION['user-created'] = CheckForUniqueUserUponCreation($firstName, $lastName, $email, $pass);
            session_destroy();
        }
        elseif ($website != NULL && $guestBlogger != NULL && $email != NULL && $pass != NULL && $linkExchanger != NULL)
        {

            $_SESSION['user-created'] = CheckForUniqueUserUponCreation($firstName, $lastName, $email, $pass);

            $userId = getUserId($email);
            $conn = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
            $conn->InsertYesOrNoLeGb($website, $linkExchanger, $guestBlogger, $userId);


            session_destroy();
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