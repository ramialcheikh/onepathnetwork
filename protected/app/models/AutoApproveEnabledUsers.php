<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class AutoApproveEnabledUsers extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'auto_approve_enabled_users';
    public $timestamps = false;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public function user()
    {
        return $this->belongsTo('User');
    }

}
