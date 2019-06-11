<?php


class DatabaseConnection
{
    private $UserName;
    private $Password;
    private $ConnectionString;
    public $ConnectionStatus;
    public $DatabaseConnection;

    public function __construct($UserName, $Password, $ConnectionString)
    {
        $this->UserName = $UserName;
        $this->Password = $Password;
        $this->ConnectionString = $ConnectionString;
        $this->Connect();

    }

    private function Connect()
    {
        try
        {
            $this->DatabaseConnection = new PDO($this->ConnectionString, $this->UserName, $this->Password, array(PDO::ATTR_PERSISTENT => true));



        }
        catch (PDOException $error)
        {
            return '<p>'. $error .'</p>';
            die();
        }


    }


    public function CloseConnection()
    {
        $this->DatabaseConnection = null;
    }

}