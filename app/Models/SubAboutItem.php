<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAboutItem extends Model
{
    protected $fillable = ['about_item_id','title'];

    public function aboutItem()
    {
        return $this->belongsTo(AboutItem::class);
    }

    public function contents()
    {
        return $this->hasMany(SubAboutItemContent::class);
    }
}
