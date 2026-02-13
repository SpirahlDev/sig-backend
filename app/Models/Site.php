<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Site extends Model
{
    use HasFactory;

    protected $table = 'site';

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'description',
        'lat',
        'lon',
        'city',
        'site_creation_date',
        'site_type_id',
    ];

    protected function casts(): array
    {
        return [
            'site_creation_date' => 'date',
        ];
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'site_has_photo')
            ->withPivot('created_at');
    }

    public function siteType()
    {
        return $this->belongsTo(SiteType::class, 'site_type_id');
    }
}
