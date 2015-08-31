<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 28/03/15
 * Time: 4:29 AM
 */

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;


class Category extends Eloquent implements SluggableInterface {
    use SluggableTrait;

    protected $table = "categories";
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $sluggable = array(
        'build_from' => 'name',
        'save_to'    => 'slug',
    );
}
