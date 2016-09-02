<?php
class Db
{
    const HOST = '127.0.0.1';
    const USER = 'root';
    const PASS = 'root';
    
    protected $_adminUsername = 'admin';
    protected $_adminPassword = 'e83bdb10d12b31f4f7b83546d657c093:qX'; //changeme1
    protected $_adminEmail = 'test@example.com';
    protected $_c;
   
    public function run ($database, $url) {
        $this->_c = new PDO("mysql:host=".self::HOST."; dbname=".$database."", self::USER, self::PASS);
        $this->_c->exec("SET CHARACTER SET utf8");
        $this->_c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->_createAdmin();
        $this->_setUrls($url);
        $this->_disableMerge();
    }
    
    protected function _createAdmin() {    
        try {
            $sql = "UPDATE `admin_user`   
                SET `username` = :username, 
                `password` = :password,
                `email` = :email
                WHERE `user_id` = :user_id";
              
            $statement = $this->_c->prepare($sql);
            $statement->bindValue(":username", $this->_adminUsername);
            $statement->bindValue(":password", $this->_adminPassword);
            $statement->bindValue(":email", $this->_adminEmail);
            $statement->bindValue(":user_id", 1);
            
            $count = $statement->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    protected function _setUrls($url) {
        try {
            $sql = "UPDATE `core_config_data`
                SET `value` = :url
                WHERE `path` like :path";
        
            $statement = $this->_c->prepare($sql);
            $statement->bindValue(":url", $url);
            $statement->bindValue(":path", '%secure/base_url%');
            $count = $statement->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    protected function _disableMerge() {
        try {
            $sql = "UPDATE `core_config_data`
                SET `value` = :value
                WHERE `path` like :path";
    
            $statement = $this->_c->prepare($sql);
            $statement->bindValue(":value", 0);
            $statement->bindValue(":path", '%merge%');
            $count = $statement->execute();
    
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
    
    $db = new Db; 

    if (!$argv[1] || !$argv[2]) {
        $db->showHelp();
        exit;        
    }
    
    $database = $argv[1];
    $url = $argv[2];  
    
    //var_dump($argv[1] . $argv[2]); exit;
    
    $db->run($database, $url);
