<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class IterinaryPhoto
 * @property-read image_path
 * @property-write image_path
 * @property iterinary_id
 * @package App
 */
class IterinaryPhoto extends Model {

	//
    protected $table = 'iterinary_photos';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function iterinary()
    {
        return $this->belongsTo('App\Iterinary');
    }
}
