<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'name','mobile','address','email', 'logo' , 'category_id' , 'active' ,'created_at', 'updated_at'
    ];

    protected $hidden = [  'category_id'  ];

    public function scopeActive($query) {
        return $query -> where('active' , 1 );
    }

    public function getLogoAttribute($val) {

        return ($val !== null ) ? asset ('assets/' . $val) : "";
    }

    public function scopeSelection($query) {
        return $query ->select('id' , 'category_id' , 'logo' , 'mobile');
    }

}
