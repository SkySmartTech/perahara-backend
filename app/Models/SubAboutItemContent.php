<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAboutItemContent extends Model
{
    protected $fillable = ['sub_about_item_id','title','short_description','image'];

    public function subAboutItem()
    {
        return $this->belongsTo(SubAboutItem::class);
    }

    public function details()
    {
        return $this->hasMany(SubAboutItemContentDetail::class, 'sub_about_item_content_id');
    }
}
