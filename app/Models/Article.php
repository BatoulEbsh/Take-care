<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $input)
 * @method static find($id)
 */
class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'body',
        'image',
        'admin_id'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
