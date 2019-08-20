<?php

include 'config.php';

foreach ( glob("includes/*.php") as $file)
{
    include_once $file;
}


/**
 * Description: Checks titles before inserting post.
 * @param $userId
 * @param $postTitle
 * @param $content
 */
function CheckCurrentUserPostTitlesAndInsert($userId, $postTitle, $content, $type)
{

    $storedPostTitles = GetAllPostTitles();

    $check = CheckPostTitles($postTitle);

    $dupCheck = CheckPostTitles($postTitle.' DUPLICATE');

    if($check == false)
    {
        InsertPostQuery($userId, $postTitle, $content, $type);
    }
    elseif ($dupCheck == false && $check == true)
    {

        $duplicateTitle = $postTitle . ' DUPLICATE';
        InsertPostQuery($userId, $duplicateTitle, $content, $type);

    }
    else{
        echo '<h3>Please choose a different title</h3>';
    }
}



function CheckCurrentUserLinkTitlesAndInsert($currentUser, $linkUrl, $linkDescription, $linkTitle)
{
    $currentUserLinkTitles = SelectAllUserLinkTitles($currentUser);


    if(!in_array($linkTitle, $currentUserLinkTitles))
    {
        InsertLink($currentUser, $linkUrl, $linkDescription, $linkTitle);
    }
}

/**
 * @param $postId
 * @return array
 */
function GetCurrentPageContentWithPostId($postId)
{
    $postObjectArray = array();

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectContentWithPostId($postId);

    $query->CloseConnection();

    if($results != null)
    {
        foreach ($results as $result)
        {

            array_push($postObjectArray, $result);
        }
    }

    return $postObjectArray;

}

/**
 * Description: Gets the current page title and content based upon the permalink
 * @param $permalink
 * @return array
 */
function GetCurrentPageContentWithPermalink($permalink)
{
    $postObjectArray = array();

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectContentWithPermalink($permalink);

    $query->CloseConnection();

    if($results != null)
    {
        foreach ($results as $result)
        {

            array_push($postObjectArray, $result);
        }
    }
    return $postObjectArray;

}

/**
 * Description: Gets the current linq Page based upon permalink
 * @param $permalink
 * @return array
 */
function GetCurrentLinkPageContentWithPermalink($permalink)
{

    $postObjectArray = array();

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectContentWithPermalinkFromLinks($permalink);





    if($results != null)
    {

        foreach ($results as $result)
        {
            array_push($postObjectArray, $result);
        }
    }

    $getLinqProviderEmail = $query->SelectAllCurrentUserWithId($postObjectArray[0]['UserId']);

    if($getLinqProviderEmail != null)
    {
        foreach ($getLinqProviderEmail as $each)
        {
            array_push($postObjectArray, $each['Email']);
        }
    }

    $query->CloseConnection();

    return $postObjectArray;

}

/**
 * @return mixed
 */
function GetPostInfoFromDatabase()
{

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectAll('backlinqs_post');


    $query->CloseConnection();

    echo '<ul>';
    foreach ($results as $result)
    {
        echo '<li><a href="?id=' . $result['PostId'] . '">' . $result['Title'] . '</a></li>';

    }
    echo '</ul>';

    //return $results;

}

function SelectAllLinksFromDatabase()
{

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectAll('backlinqs_links');

    $query->CloseConnection();

    return $results;

}

function SelectAllPostFromDatabase()
{

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectAll('backlinqs_post');

    $query->CloseConnection();

    return $results;

}

/**
 * Description: Inserts a post into the database
 * @param $currentUser
 * @param $title
 * @param $content
 */
function InsertPostQuery($currentUser, $title, $content, $type)
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);


    $insert = $query->InsertPost($currentUser, $title, $content, $type);



    $query->CloseConnection();

}

function InsertLink($currentUser, $linkUrl, $linkDescription, $linkTitle)
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);



    $insert = $query->InsertLink($currentUser, $linkUrl, $linkDescription, $linkTitle);


    $query->CloseConnection();

}

/**
 * Description: Selects all of the current users' post information
 * @param $currentUser
 * @return array
 */
function SelectAllCurrentUser($currentUser)
{
    $PostArray = array();

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
    $selectAll = $query->SelectAllCurrentUser("backlinqs_post", $currentUser);
    $query->CloseConnection();

    foreach($selectAll as $item)
    {
        array_push($PostArray, $item);
    }

    return $PostArray;

}

/**
 * Description: Selects all the post titles
 * @return array of post titles
 */
function GetAllPostTitles()
{
    $PostTitlesArray = array();

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
    $selectAll = $query->SelectAllPostTitles();

    $query->CloseConnection();

    foreach($selectAll as $item)
    {
        array_push($PostTitlesArray, $item['Title']);
    }

    return $PostTitlesArray;

}

/**
 * Description: Checks all the post titles for duplicates.
 * @param $title
 * @return bool
 */
function CheckPostTitles($title)
{
    $postTitles = GetAllPostTitles();

    $trueOrFalse = in_array($title, $postTitles);

    return $trueOrFalse;
}

function SelectAllUserLinkTitles($currentUserId)
{
    $PostTitleArray = array();


    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
    $selectAll = $query->SelectAllUserLinksByUserId((int) $currentUserId);
    $query->CloseConnection();



    foreach($selectAll as $item)
    {
        array_push($PostTitleArray, $item['Title']);
    }

    return $PostTitleArray;

}

function UpdatePost()
{

}

function GetAllUsers()
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $allUsers = $query->SelectAllUsers();

    $query->CloseConnection();

    return $allUsers;
}

function CreateNewUser($firstName, $lastName, $email, $passHash, $role)
{

        $newUserId = '';
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $query->CreateUser($firstName, $lastName, $email, HashPass($passHash));

        $getNewUserId = $query->GetCurrentUserId($email);

        foreach ($getNewUserId as $each)
        {
           $newUserId = $each['ID'];
        }
        $query->InsertNewUserRole($newUserId, $role);

        $query->InsertNewUserInfoId($newUserId);
        $query->CloseConnection();

        $user = new Users(USER, PASS, CONNETIONSTRING);

        $user->VerifyEmail($email);

}

function UserLogin($email)
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $query->Login($email);

    $query->CloseConnection();
}

function CheckForUniqueUserUponCreation($firstName, $lastName, $email, $passHash)
{
    $emailArray = array();
    $allUsers = GetAllUsers();


    foreach ($allUsers as $user)
    {
        array_push($emailArray, $user['Email']);
    }

    if(!in_array($email, $emailArray))
    {
        if($email != null || $email != '')
        {

            if($_SESSION['confirmPassword'] == $_SESSION['inputPassword'])
            {
                if($firstName == NULL || $lastName == NULL)
                {
                    $firstName = '';
                    $lastName = '';
                }
                CreateNewUser($firstName, $lastName, $email, $passHash, 5);
                //echo 'User Created';
//                session_destroy();
                return false;
            }
            else{
                echo 'Confirm password and password must match! Please try again.';
//                session_destroy();
                return true;
            }
        }
    }
    else {
        echo 'User Already Exists';
        session_destroy();
        return true;
    }

}

function HashPass($userPass)
{
    return password_hash($userPass, PASSWORD_DEFAULT);
}

function VerifyHashPass($pass, $hash)
{
    return password_verify($pass, $hash);
}

function VerifyUser($email, $pass)
{

    $allUsers = GetAllUsers();

    foreach ($allUsers as $key=>$value)
    {

        if($value['Email'] == $email)
        {

            $verify = VerifyHashPass($pass, $value['PassHash']);


           if($verify == true)
           {

               UserLogin($email);
               unset($_SESSION['inputPassword']);
               return true;
           }
           else{
               //session_destroy();
               return false;
           }
        }
    }
}

function CheckUserStatusQuery($user)
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $userStatus= $query->CheckLoginStatus($user);

    $query->CloseConnection();

    return $userStatus;
}



function CheckUserStatus($user)
{
    $status = CheckUserStatusQuery($user);

    foreach($status as $item)
    {
        if( $item['Status'][0] == "0")
        {
            return false;
        }
        elseif ( $item['Status'][0] == "1")
        {
            return true;
        }
        else
        {
            return false;

        }
        break;
    }
}

function LogOutUserQuery($user)
{

    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $userStatus= $query->LogOut($user);

    $query->CloseConnection();



    return $userStatus;
}

function UpdateUserInfo($id)
{
    if(isset( $_SESSION['company']) || isset( $_SESSION['facebook']) || isset( $_SESSION['youtube']) || isset( $_SESSION['instagram']) || isset( $_SESSION['phone']) || isset( $_SESSION['company']))
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $userStatus= $query->UpdateUserInfoWithId($id);

        $query->CloseConnection();
    }
}

function CheckUserInfoTable($id)
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $userStatus= $query->SelectUserInfo($id);

    $query->CloseConnection();
    $array = array();

    $arrayPush = array();

    foreach ($userStatus as $each)
    {
        array_push($arrayPush, $each);
    }

    if(!isset($arrayPush[0]['UserID']))
    {
        return false;
    }
    else{
        return true;
    }



}

function getUserId($email)
{
    $user = new Users(USER, PASS, CONNETIONSTRING);

    $id = $user->GetCurrentUserId($email);

    foreach ($id as $item)
    {
        $id = $item['ID'];
    }

    return $id;
}