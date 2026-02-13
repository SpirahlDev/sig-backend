<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photo';

    const UPDATED_AT = null;

    protected $fillable = [
        'url',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'double',
        ];
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'site_has_photo')
            ->withPivot('created_at');
    }
}
