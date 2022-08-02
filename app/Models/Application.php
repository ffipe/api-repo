<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name'];

    // protected $with = ['role'];

    // public function permissions()
    // {
    //     return $this->hasMany(Permission::class);
    // }

    // public function role()
    // {
    //     return $this->hasOneThrough(Permission::class, Role::class);
    // }
}
