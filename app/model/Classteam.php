<?php

namespace app\model;

use support\Model;

/**
 *
 */
class Classteam extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'sqlite';
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classteams';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    
}
