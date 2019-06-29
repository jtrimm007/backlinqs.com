<?php


class Users extends DatabaseQuery
{



    public $email;
    public $firstName;
    public $lastName;
    private $pass;
    public $role;
    public $loginStatus;
    public $userId;

    public function __construct($UserName, $Password, $ConnectionString)
    {
        parent::__construct($UserName, $Password, $ConnectionString);
    }

    public function GetUser($email)
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $results = $query->SelectAllCurrentUserWithEmail($email);

        $query->CloseConnection();

        echo '<ul>';

        foreach ($results as $result)
        {
            echo '<li><a href="?id=' . $result['PostId'] . '">' . $result['Title'] . '</a></li>';
        }
        echo '</ul>';
    }

    public function GetUserRole($email)
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $results = $query->SelectUserRole($email);

        $query->CloseConnection();

        foreach ($results as $each)
        {
            $this->role = $each['role_id'];
        }
    }

    function GetUserStatus($user)
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $userStatus= $query->CheckLoginStatus($user);

        $query->CloseConnection();

        foreach($userStatus as $item)
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

    /**
     * Description: Gets the current user id when the user email is fed into the function
     * @param $user
     * @return mixed
     */
    function GetCurrentUserIdQuery($user)
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $userStatus= $query->GetCurrentUserId($user);

        foreach ($userStatus as $id)
        {

            return $id['ID'];

        }

        $query->CloseConnection();

    }

    function SetCurrentUserIdQuery($user)
    {
        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

        $userStatus= $query->GetCurrentUserId($user);

        foreach ($userStatus as $id)
        {

            $this->userId = $id['ID'];
            setcookie('user-id', $this->userId, time()+3600);

        }

        $query->CloseConnection();

    }

    public function VerifyEmail($email)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isHTML(true);
        $mail->setFrom('joshua@backlinqs.com', 'Joshua');
        $mail->addAddress($email, 'New User');
        $mail->Subject  = 'Welcome to Backlinqs.com, Please Verify Account';
        $mail->Body     = 'Thank you for joining us, we are happy to have you! Please <a href="https://backlinqs.com/login" target="_blank">click here</a> to verify your account.';
        if(!$mail->send()) {
            //echo 'Message was not sent.';
            //echo 'Mailer error: ' . $mail->ErrorInfo;
        } else {
//            header("Location: https://backlinqs.com/verify-account");
//            exit();
        }


    }
}