<?php
/**
 * Class DatabaseQuery
 */

class DatabaseQuery extends DatabaseConnection
{

    /**
     * DatabaseQuery constructor.
     * @param $UserName
     * @param $Password
     * @param $ConnectionString
     */
    public function __construct($UserName, $Password, $ConnectionString)
    {
        parent::__construct($UserName, $Password, $ConnectionString);
    }

    /**
     * Description: Selects all from a table
     * @param $table
     * @return mixed
     */
    public function SelectAll($table)
    {

        return $this->DatabaseConnection->query('SELECT * FROM ' . $table . ' ');
    }

    /**
     * Description: Select a post based upon post ID.
     * @param $PostId
     * @return mixed
     */
    public function SelectContentWithPostId($PostId)
    {
        return $this->DatabaseConnection->query('SELECT * FROM backlinqs_post WHERE PostId = ' . $PostId . ' ');
    }

    /**
     * Description: Select all the blog post
     * @return mixed
     */
    public function SelectAllBlogPost()
    {
        return $this->DatabaseConnection->query('SELECT * FROM `backlinqs_post` WHERE `post_type` = "blog"');
    }

    public function SelectPostIdWithPermalink($permalink)
    {
        return $this->DatabaseConnection->query('SELECT PostId FROM backlinqs_post WHERE permalink = "' . $permalink . '" ');
    }


    /**
     * Description: Select a post based on its permalink
     * @param $permalink
     * @return mixed
     */
    public function SelectContentWithPermalink($permalink)
    {
        return $this->DatabaseConnection->query('SELECT * FROM `backlinqs_post` WHERE `permalink` = "' . $permalink . '" ');
    }


    /**
     * Description: Selects title and content for the home page based upon home page permalink
     * @param $permalink
     * @return mixed
     */
    public function SelectTitleAndContentForHomePage($permalink)
    {
        return $this->DatabaseConnection->query('SELECT `Title`, `Content` FROM `backlinqs_post` WHERE `permalink` = "' . $permalink . '" ');

    }


    /**
     * Description: Select all the information about a Link base upon its permalink
     * @param $permalink
     * @return mixed
     */
    public function SelectContentWithPermalinkFromLinks($permalink)
    {
        return $this->DatabaseConnection->query('SELECT * FROM `backlinqs_links` WHERE `permalink` = "' . $permalink . '" ');
    }

    /**
     * Description: Select all the users information based upon their email
     * @param $table
     * @param $currentUserEmail
     * @return mixed
     */
    public function SelectAllCurrentUserWithEmail($currentUserEmail)
    {
        return $this->DatabaseConnection->query('SELECT * FROM backlinqs_users WHERE Email = ' . $currentUserEmail.' ');
    }

    /**
     * Description: Select all the post titles
     * @return mixed
     */
    public function SelectAllPostTitles()
    {
        return $this->DatabaseConnection->query('SELECT `Title` FROM `backlinqs_post`');

    }


    /**
     * Select all the information about a user from the users table with their id.
     * @param $currentUserId
     * @return mixed
     */
    public function SelectAllCurrentUserWithId($currentUserId)
    {
        return $this->DatabaseConnection->query('SELECT * FROM `backlinqs_users` WHERE ID = "' . $currentUserId.'" ');
    }

    /**
     * Description: Selects all the user info from backlinqs_user_info
     * @param $email
     * @return mixed
     */
    public function SelectUserInfo($id)
    {
        return $this->DatabaseConnection->query('SELECT * FROM `backlinqs_user_info` WHERE UserID = ' . $id.' ');
    }

    /**
     * Description: Select a users role ID based upon their email
     * @param $email
     * @return mixed
     */
    public function SelectUserRole($email)
    {
        return $this->DatabaseConnection->query('SELECT role_id FROM backlinqs_user_role INNER JOIN backlinqs_users ON backlinqs_user_role.user_id = backlinqs_users.ID WHERE backlinqs_users.Email = \''.$email.'\' ');
    }

    /**
     * Description: Select all the users
     * @return mixed
     */
    public function SelectAllUsers()
    {
        return $this->DatabaseConnection->query('SELECT * FROM backlinqs_users');
    }


    public function SelectAllUserLinksByUserId($id)
    {
        return $this->DatabaseConnection->query('SELECT * FROM backlinqs_links INNER JOIN backlinqs_users ON backlinqs_users.ID = backlinqs_links.UserId WHERE backlinqs_users.ID = '.$id.' ');
    }

    /**
     * Description: Insert a post
     * @param $userId
     * @param $title
     * @param $content
     * @param $type
     * @return mixed
     */
    public function InsertPost( $userId, $title, $content, $type)
    {
        $toLower = strtolower($title);
        $replace = str_replace(array("_","?","!"," ", "$", "&", "#"), "-", $toLower);
        $permalink = trim($replace);

        $htmlEncode = htmlentities($content, ENT_NOQUOTES, "ISO-8859-15");

        $htmlEncode2 = htmlentities($content, ENT_QUOTES, "ISO-8859-15");


        $htmlSpecialChars = htmlspecialchars($content);

        $currentDate = date("Y-m-d h:i:sa");
        $sql = "INSERT INTO `backlinqs_post` (UserId, Title, Content, DateCreated, permalink, post_type) VALUES ('$userId','$title','$content','$currentDate', '$permalink', '$type')";
        $stmt = $this->DatabaseConnection->query($sql);

        return $stmt;
    }

    public function UpdatePost( $title, $content, $permalink, $type, $postId)
    {

        $htmlEncode = htmlentities($content, ENT_NOQUOTES, "ISO-8859-15");

        $htmlEncode2 = htmlentities($content, ENT_QUOTES, "ISO-8859-15");


        $htmlSpecialChars = htmlspecialchars($content);


        $currentDate = date("Y-m-d h:i:sa");
        $sql = "UPDATE `backlinqs_post` SET Title = '".$title."', Content = '".$content."', permalink = '".$permalink."', post_type = '".$type."'WHERE PostId = ".$postId." ";

        //UPDATE `backlinqs_post` SET Title = 'About', Content = 'Testing', permalink = 'about', post_type = 'page' WHERE PostId = 34

        $stmt = $this->DatabaseConnection->query($sql);

        return $stmt;
    }

    /**
     * Description: Insert a Link
     * @param $userId
     * @param $linkUrl
     * @param $linkDescription
     * @param $linkTitle
     * @return mixed
     */
    public function InsertLink( $userId,  $linkUrl, $linkDescription, $linkTitle)
    {
        $toLower = strtolower($linkTitle);
        $replace = str_replace(array("_","?","!"," ", "$", "&", "#"), "-", $toLower);
        $permalink = trim($replace);

        $currentDate = date("Y-m-d h:i:sa");
        $sql = "INSERT INTO `backlinqs_links` (`UserId`, `LinkUrl`, `Date`, `Content`, `Title`, `permalink`) VALUES ('$userId', '$linkUrl', '$currentDate', '$linkDescription', '$linkTitle', '$permalink')";
        $stmt = $this->DatabaseConnection->query($sql);

        return $stmt;
    }


    /**
     * Description: Create a new user
     * @param $fistName
     * @param $lastName
     * @param $email
     * @param $passHash
     */
    public function CreateUser($fistName, $lastName, $email, $passHash)
    {
        $loginStatus = 0;
        $sql = "INSERT INTO backlinqs_users (FirstName, LastName, Email, PassHash, Status) VALUES ('$fistName','$lastName','$email', '$passHash', '$loginStatus' )";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$fistName, $lastName, $email, $passHash]);
    }

    public function InsertNewUserRole($userId, $role)
    {
        $userId = (int) $userId;
        $role = (int) $role;


        $sql = "INSERT INTO backlinqs_user_role (user_id, role_id) VALUES ('$userId','$role' )";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$userId, $role]);
    }

    public function InsertNewUserInfoId($userId)
    {
        $userId = (int) $userId;

        $sql = "INSERT INTO backlinqs_user_info (UserID) VALUES ('$userId')";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$userId]);
    }


    /**
     * Description: Login query to set user status to 1 i.e. true or logged in.
     * @param $email
     */
    public function Login($email)
    {
        $status = 1;
        $sql = "UPDATE backlinqs_users SET  Status = '$status'  WHERE Email = '$email'";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$status, $email]);
    }

    /**
     * Description: Logs out a user based upon their email
     * @param $email
     */
    public function LogOut($email)
    {
        $status = 0;
        $sql = "UPDATE backlinqs_users SET  Status = '$status'  WHERE Email = '$email'";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$status, $email]);
    }

    /**
     * Description: Check a users login status based upon their email
     * @param $email
     * @return mixed
     */
    public function CheckLoginStatus($email)
    {
        return $this->DatabaseConnection->query("SELECT `Status` FROM `backlinqs_users` WHERE Email = '$email' ");
    }

    /**
     * Description: Gets a users ID based upon their email.
     * @param $email
     * @return mixed
     */
    public function GetCurrentUserId($email)
    {
        return $this->DatabaseConnection->query("SELECT `ID` FROM `backlinqs_users` WHERE Email = '$email' ");

    }

    public function SearchForKeywordInLinks($keywords)
    {
        return $this->DatabaseConnection->query("SELECT * FROM `backlinqs_links` WHERE ( Content LIKE '%$keywords%' OR Title LIKE '%$keywords%' ) ORDER  BY `backlinqs_links`.`Date` DESC");
    }

    public function UpdateUserInfoWithId($id)
    {
        $company = $_SESSION['company'];
        $website = $_SESSION['website'];
        $facebook = $_SESSION['facebook'];
        $youtube = $_SESSION['youtube'];
        $instagram = $_SESSION['instagram'];
        $phone = $_SESSION['phone'];
        $about = $_SESSION['about'];


        $sql = "UPDATE backlinqs_user_info SET  Company = '$company', Website = '$website', Facebook = '$facebook', Youtube = '$youtube', Instagram = '$instagram', Phone = '$phone', About = '$about'  WHERE UserID = '$id'";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$company, $facebook, $youtube, $instagram, $phone, $about, $id]);
    }

    public function LazyLoadQuery($start, $limit)
    {
        return $this->DatabaseConnection->query("SELECT * FROM `backlinqs_scrap` LIMIT ".$start.", ". $limit." ");

    }
    public function SelectEmailFromScrap($url)
    {
        return $this->DatabaseConnection->query("SELECT `email` FROM `backlinqs_scrap` WHERE url = '$url' ");

    }

    public function InsertYesOrNoLeGb($website, $le, $gl, $userId)
    {

        $sql = "UPDATE backlinqs_user_info SET  Website = '$website', link_exchange = '$le', guest_blogger = '$gl'  WHERE UserID = '$userId'";
        $stmt= $this->DatabaseConnection->prepare($sql);
        $stmt->execute([$website, $le, $gl, $userId]);
    }
}