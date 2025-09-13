<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutItem extends Model
{
    protected $fillable = ['name','description','image'];

    public function subItems()
    {
        return $this->hasMany(SubAboutItem::class);
    }
}
