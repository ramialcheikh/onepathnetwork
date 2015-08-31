<?php
class @@model@@ extends Eloquent {
    protected $table = "@@table@@";
    public static $snakeAttributes = false;

    public function getActiveAttribute($value) {
        return ($value ? true : false);
    }
}