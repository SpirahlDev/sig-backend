<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteType extends Model
{
    use HasFactory;

    protected $table = 'site_type';

    const UPDATED_AT = null;

    protected $fillable = [
        'code',
        'label',
    ];

    public function sites()
    {
        return $this->hasMany(Site::class, 'site_type_id');
    }
}