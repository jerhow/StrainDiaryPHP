<?php

// namespace Common;

class Common {

    public static function nonce() {
        return hrtime(true);
    }
}
