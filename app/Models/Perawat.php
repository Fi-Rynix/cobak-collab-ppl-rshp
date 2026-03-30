<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perawat extends Model
{
    protected $table = 'perawat';
    protected $primaryKey = 'idperawat';
    public $timestamps = false;

    protected $fillable = ['alamat', 'no_hp', 'pendidikan', 'jenis_kelamin', 'iduser', 'deleted_at', 'deleted_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }
}

?>