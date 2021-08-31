<?php

namespace Azhida\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestBinaryTreeMaxDepth extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'max_depth',
        'leg',
    ];
}
