<?php
class List extends Eloquent {
    protected $table = "undefined";
    public static $snakeAttributes = false;

    public function getActiveAttribute($value) {
        return ($value ? true : false);
    }
}