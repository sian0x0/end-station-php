<?php

class Auth
{
    public function __construct(
        protected PDO $db, // protected: will be saved into a property
    ) {}
    
    //Handle adding users
    public function add_user(string $username, string $password, int $role): int | false {
        $username = trim( $username );
        $password = trim( $password );

        $hash = password_hash( $password, PASSWORD_DEFAULT );

        //TODO: don't add user if user exists

        // don't add a new user if hashing doesn't work
        if( $hash === false ) { 
            return false;
        } 
        if( $hash === null ) { 
            throw new \Exception ("Bad hashing algorithm");
            return false;
        } 

        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        try {
            $stmt->execute( [
            ":username" => $username,
            ":password" => $hash,  //NB not $password!
            ":role"  => $role,
            ] );
        } catch(\PDOException $e) {
            error_log($e.getMessage());
            return false;
        }

        $id = $this->db->lastInsertId(); // get the id of the last user added to return/check for errors
        if($id === false) {
            return false;
        }
        return intval( $id ); //note: use intval to cast to int
    }

    public function authenicate(string $username, string $password): int|false {
        $username = trim( $username );
        $password = trim( $password );

        try {
            $stmt = $this->db->prepare("SELECT id, password FROM users WHERE username = :username");
            $stmt->execute([":username" => $username,]); //note: assigns the variable to the sql placeholder on execution
        } catch (\PDOException $e) {
            error_log( $e->getMessage());
            return false;
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // get array of user data from db
        if ($user === false) {return false;}
        
        $verify = password_verify($password, $user["password"]); // compare the entered password to the db password ($user array)
        if ($verify === true) {
            return intval($user["id"]); //nb make sure to fetch by array key "id" and not variable $id
        }

        return false; // if check fails, ie there was no successful return until now
    }
//TODO: change to get user data (all including visits)
    public function get_user_role (int $user_id): int|bool {
        try {
            $stmt = $this->db->prepare("SELECT role FROM users where id = :user_id");
            $stmt->execute(
                [":user_id => $user_id,"]
            );
        } catch (\PDOException $e) {
            error_log( $e->getMessage());
            return false;
        }
        $role = $stmt->fetchColumn();

        if ($role === false) {
            return false;
        }

        return intval($role);

    }

    public function log_user_in(int $user_id) : void {
        session_regenerate_id(true); //for security - basic!
        $_SESSION["logged_in_user"] = $user_id;
    }
    public function log_user_out() : void {
        $_SESSION["logged_in_user"] =null;
    }

    public function logged_in_user() : int|false {
        if(! isset($_SESSION["logged_in_user"])) {
            return false;
        }

        if(! ($_SESSION["logged_in_user"])) {
            return false;
        }

        return intval($_SESSION["logged_in_user"]);
    }
    
}