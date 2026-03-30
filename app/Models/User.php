<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'iduser';
    public $timestamps = false;

    protected $fillable = ['nama', 'email', 'password', 'deleted_at', 'deleted_by'];

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class, 'iduser', 'iduser');
    }

    public function Pemilik()
    {
        return $this->hasOne(Pemilik::class, 'iduser', 'iduser');
    }

    public function Dokter()
    {
        return $this->hasOne(Dokter::class, 'iduser', 'iduser');
    }

    public function Perawat()
    {
        return $this->hasOne(Perawat::class, 'iduser', 'iduser');
    }

}
?>