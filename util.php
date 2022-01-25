<?php

class Util {

    /**
     * Returns a string
     */
    public static function confirmationCode() {
        $bytes = random_bytes(20);
        return bin2hex($bytes);
    }
}
