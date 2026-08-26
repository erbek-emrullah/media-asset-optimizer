<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
    'name',
    'description',
    'mime_type',
    'folder',
    'file_checksum',
    'path',
];
}
