<?php

// namespace Common;

define('URL_BASE', '/straindiary');
define('BCRYPT_COST', 8);

class Common {

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
}
