<?php

class Util {

    /**
     * Returns a string
     */
    public static function confirmationCode() {
        $bytes = random_bytes(20);
        return bin2hex($bytes);
    }

    /**
     * Returns a number derived from the system's high resolution time
     * (integer on 64 bit platforms, float on 32 bit platforms)
     */
    public static function nonce() {
        return hrtime(true);
    }
}
