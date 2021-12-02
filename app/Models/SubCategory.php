<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategory;

class SubCategory extends Model
{

    protected $table = 'sub_categories';

    protected $fillable = [
        'translation_lang', 'parent_id' , 'translation_of','name','slug' , 'photo' , 'active' ,'created_at', 'updated_at'
    ];


    public function scopeActive($query) {  // #09

        return $query-> where('active',1);

    }

    public function scopeSelection($query) {
        return $query->select('id' , 'parent_id' , 'translation_lang' , 'name' , 'slug' , 'photo' , 'active' , 'translation_of');

    }

    public function getPhotoAttribute($val)  //#19
    {
        return ( $val !== null ? asset('assets/' . $val) : '' );

    }

    public function getActive () {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }


    // this relationship to get maincategoris from MainCategories
    public function maincategory() {

        return $this->belongsTo(MainCategory::class , 'category_id' , 'id');

    }

}
