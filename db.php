<?php

class Db {
    public static function dbh() {
        $db_host = getenv('DATABASE_HOST', true);
        $db_name = getenv('DATABASE_NAME', true);
        $db_user = getenv('DATABASE_USER', true);
        $db_pwd = getenv('DATABASE_PWD', true);

        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=UTF8";

        try {
            $dbh = new PDO($dsn, $db_user, $db_pwd);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }

        // set the PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
    }

    public static function authenticate($un = '', $pw = '') {

        global $dbh;

        if($un === '' || $pw === '') {
            return false;
        }

        // $pw_hashed = password_hash($pw, PASSWORD_BCRYPT, ["cost" => BCRYPT_COST]);
        // password_verify($pw, $hashed_password);

        try {
            $sql = "" .
                "SELECT id AS user_id, un, pw, nickname, created_at " .
                "FROM t_user " .
                "WHERE un = :un";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':un', $un);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
        }

        if(count($rows) !== 1) {
            return false;
        }

        if(!password_verify($pw, $rows[0]['pw'])) {
            return false;
        }

        return $rows;
    }

    /**
     * Returns a boolean.
     * Returns false if an empty string is passed in.
     */
    public static function userNameExists($un = '') {
        global $dbh;

        if($un === '') {
            return false;
        }

        try {
            $sql = "" .
                "SELECT id " .
                "FROM t_user " .
                "WHERE un = :un";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':un', $un);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
        }

        if(count($rows) === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Nicknames are also unique in the DB schema, so we need to check when
     * one is requested.
     * 
     * Returns a boolean.
     * Returns false if an empty string is passed in.
     */
    public static function nicknameExists($nickname = '') {
        global $dbh;

        if($nickname === '') {
            return false;
        }

        try {
            $sql = "" .
                "SELECT id " .
                "FROM t_user " .
                "WHERE nickname = :nickname";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':nickname', $nickname);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
        }

        if(count($rows) === 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function addNewUser($un, $pw, $nickname, $confirmation_code) {
        global $dbh;

        try {
            $sql = "" .
                "INSERT INTO t_user " .
                "(un, pw, nickname, confirmation_code) " .
                "VALUES " .
                "(:un, :pw, :nickname, :confirmation_code) ";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':un', $un);
            $stmt->bindValue(':pw', $pw);
            $stmt->bindValue(':nickname', $nickname);
            $stmt->bindValue(':confirmation_code', $confirmation_code);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
        }

        return true;
    }
}
