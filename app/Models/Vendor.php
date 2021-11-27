<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'latitude' , 'longitude' , 'name', 'category_id','mobile','email', 'password' , 'address', 'logo'  , 'active' ,'created_at', 'updated_at'
    ];

    protected $hidden = [  'category_id' ];

    public function scopeActive($query) {
        return $query -> where('active' , 1 );
    }

    public function getLogoAttribute($val) {

        return ($val !== null ) ? asset ('assets/' . $val) : "";
    }

    public function scopeSelection($query) {
        return $query ->select('id' , 'category_id', 'active', 'name', 'address','email' , 'logo' , 'mobile');
    }

    public function category()
    {

        return $this->belongsTo('App\Models\MainCategory', 'category_id', 'id');
    }

    public function getActive () {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            // #33
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
