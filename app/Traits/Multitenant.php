<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Multitenant
{
    public static function bootMultitenant()
    {
        if (auth()->check()) {
            $url = url()->current();

            /*if (strpos($url, '/admin')) {*/
                // if (Auth::user()->is_admin != 1) {
                    static::creating(function ($model) {
                        $model->tenant_id = Auth::user()->tenant_id;
                        $model->created_by = Auth::user()->id;
                    });

                    static::updating(function ($model) {
                        $model->updated_by = Auth::user()->id;
                    });

                    static::deleting(function ($model) {
                        $model->deleted_by = Auth::user()->id;
                        $model->save();
                    });

                    static::restoring(function ($item) {
                        $item->deleted_by = null;
                    });

                    static::addGlobalScope('tenant_id', function (Builder $builder) {
                        if (auth()->check()) {
                            if ($builder->getModel() instanceof  \App\Models\BaseModel) {
                                return $builder->where('tenant_id', Auth::user()->tenant_id);
                            }
                        }
                    });
                // }
            /*}*/
        }
    }


}
