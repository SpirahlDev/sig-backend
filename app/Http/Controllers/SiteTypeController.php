<?php

namespace App\Http\Controllers;

use App\Models\SiteType;
use Illuminate\Database\Eloquent\Builder;

class SiteTypeController extends BaseCrudController
{
    protected $model = SiteType::class;

    protected $storeRules = [
        'code' => 'required|string|max:45|unique:site_type,code',
        'label' => 'required|string|max:45',
    ];

    protected $updateRules = [
        'code' => 'sometimes|string|max:45|unique:site_type,code',
        'label' => 'sometimes|string|max:45',
    ];

    protected $allowedFields = ['id', 'code', 'label'];

    protected $allowedFilters = ['code', 'label'];

    protected $searchableFields = ['code', 'label'];
}
