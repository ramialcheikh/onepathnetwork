<?php
class ViralListChanges extends Eloquent {

    protected $table = "viral_list_changes";
    protected $fillable = ['id', 'content'];
    public function getContentAttribute($value) {
        return (json_decode($value, true));
    }

    public function lists() {
        return $this->belongsTo('ViralList', 'id');
    }
}