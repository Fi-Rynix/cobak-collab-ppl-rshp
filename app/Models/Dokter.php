<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $table = 'dokter';
    protected $primaryKey = 'iddokter';
    public $timestamps = false;

    protected $fillable = ['alamat', 'no_hp', 'bidang_dokter', 'jenis_kelamin', 'iduser', 'deleted_at', 'deleted_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }
}

?>