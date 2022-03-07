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
            error_log('Error in Db::dbh() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
            die;
        }

        // set the PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
    }

    /**
     * Returns true on success, otherwise false.
     * Assumes inputs have been sanitized by the caller.
     */
    public static function updateUserName($user_id = '', $user_name = '')
    {
        global $dbh;

        if($user_id === '' || $user_name === '') {
            error_log('Error in Db::update_user_name() - one or more missing parameters');
            // Q: Should we dump some session state to the log as well?
            return false;
        }

        try {
            $sql = "" .
                "UPDATE t_user " .
                "SET un = :user_name, " .
                "confirmed = 'N' " .
                "WHERE id = :user_id ";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_name', $user_name);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error in Db::updateUserName() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
            return false;
        }

        return true;
    }

    /**
     * Returns true on success, otherwise false.
     * Assumes inputs have been sanitized by the caller.
     */
    public static function updateNickname($user_id = '', $nickname = '')
    {
        global $dbh;

        if($user_id === '' || $nickname === '') {
            error_log('Error in Db::updateNickname() - one or more missing parameters');
            // Q: Should we dump some session state to the log as well?
            return false;
        }

        try {
            $sql = "" .
                "UPDATE t_user " .
                "SET nickname = :nickname " .
                "WHERE id = :user_id ";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':nickname', $nickname);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error in Db::updateNickame() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
            return false;
        }

        return true;
    }

    /**
     * Returns a boolean:
     * true, if code is found and matches an UNCONFIRMED record
     * false, if code is found but matches a CONFIRMED record
     * false, if code is not found (or not passed in at all)
     * 
     * 'confirmation_code' column has a unique constraint, so we should never
     * get anything other than 0 or 1 rows back from the query used here.
     */
    public static function check_confirmation_code($conf_code = '') {

        global $dbh;

        if($conf_code === '') {
            return false;
        }

        try {
            $sql = "" .
                "SELECT confirmed " .
                "FROM t_user " .
                "WHERE confirmation_code = :conf_code";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':conf_code', $conf_code);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Db::check_confirmation_code() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
        }

        if(count($rows) !== 1) {
            return false;
        }

        if($rows[0]['confirmed'] === 'N') {
            return true;
        } else {
            return false;
        }

        // We should never get here
        return false;
    }

    /**
     * Returns true on success, otherwise false
     */
    public static function mark_account_confirmed($conf_code = '') {

        global $dbh;

        if($conf_code === '') {
            return false;
        }

        try {
            $sql = "" .
                "UPDATE t_user " .
                "SET confirmed = 'Y' " .
                "WHERE confirmation_code = :conf_code ";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':conf_code', $conf_code);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error in Db::mark_account_confirmed() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
        }

        return true;
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
                "WHERE un = :un " .
                "AND confirmed = 'Y'";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':un', $un);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Db::authenticate() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
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
            error_log('Error in Db::userNameExists() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
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
            error_log('Error in Db::nicknameExists() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
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
            error_log('Error in Db::addNewUser() - ' . $e->getMessage());
            if($env === 'DEV') {
                echo $e->getMessage();
            }
        }

        return true;
    }
}
