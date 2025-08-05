<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleUsuarios extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
                            'id',
                            'name',
                            'email',
                            'senha'
                          ];

    public function rules()
    {
        return [
            'name' => 'required:usuarios',
            'email' => 'required:usuarios',
            'senha' => 'required:usuarios'
        ];
    }
}
