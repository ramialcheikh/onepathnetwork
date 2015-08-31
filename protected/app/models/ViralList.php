<?php
class ViralList extends Eloquent {
    //Add taggable trait to enable tagging: https://github.com/rtconner/laravel-tagging/tree/laravel-4
    use Conner\Tagging\TaggableTrait;

    protected $table = "viral_lists";
    public static $snakeAttributes = false;
    protected $guarded = ['created_at', 'updated_at', 'id', 'creator_user_id', 'views', 'status'];

    protected $hidden = ['views', 'pendingChanges'];

    //Include virtual attributes in toJson and toArray output
    protected $appends = array('tags');


    public function getContentAttribute($value) {
        return (json_decode($value, true));
    }

    public function getTagsAttribute($value) {
        return implode(', ', $this->tagNames());
    }

    public function createdFromList() {
        return $this->belongsTo('ViralList', 'created_from_list_id');
    }

    public function category() {
        return $this->belongsTo('Category');
    }

    public function creator() {
        return $this->belongsTo('User', 'creator_user_id');
    }

    public function pendingChanges() {
        return $this->hasOne('ViralListChanges', 'id');
    }

    public function scopeExclude($query, $excludeIds) {
        return $query->whereNotIn('id', $excludeIds);
    }

    public function scopeOfCategory($query, $categoryId) {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $q) {
        return empty($q) ? $query : $query->whereRaw(
            "MATCH(title,description) AGAINST(? IN BOOLEAN MODE)",[$q]);
    }

    public function scopeApproved($query) {
        return $query->where('status', '=' ,'approved');
    }

    public function scopeLatest($query) {
        return $query->orderBy('created_at', 'desc');
    }

    public function isApproved() {
        return ($this->status == 'approved');
    }

    public function isAwaitingApproval() {
        return ($this->status == 'awaiting_approval');
    }

    public function isNotSubmitted() {
        return ($this->status == 'not_submitted');
    }

    public function isDisapproved() {
        return ($this->status == 'disapproved');
    }

    public function markAsApproved() {
        $this->status = 'approved';
    }

    public function markAsDisapproved() {
        $this->status = 'disapproved';
    }

    public function markAsSubmitted() {
        $this->status = 'awaiting_approval';
    }

    public function markAsNotSubmitted() {
        $this->status = 'not_submitted';
    }

    public function markAsOriginalCreatorNotified() {
        $this->original_creator_notified = true;
    }

    public function isCreatedBy($user) {
        return ($this->creator_user_id == $user->id);
    }

    public function hasPendingChanges() {
        return ($this->pendingChanges()->count() > 0);
    }
}