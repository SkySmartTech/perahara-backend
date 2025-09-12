<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAboutItemContentDetail extends Model
{
    protected $fillable = ['sub_about_item_content_id','title','description'];

    public function content()
    {
        return $this->belongsTo(SubAboutItemContent::class, 'sub_about_item_content_id');
    }
}
