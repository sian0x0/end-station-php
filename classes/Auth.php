<?php

class Auth
{
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

    
}