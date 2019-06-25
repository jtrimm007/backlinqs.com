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

            $this->userId = $id['ID'];
        }

        $query->CloseConnection();

    }
}