<?php

namespace Azhida\Tools\Models;

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

    public static function getTestBinaryTreeMaxDepth($id)
    {
        $testBinaryTreeMaxDepth = TestBinaryTreeMaxDepth::query()->find($id);
        if (!$testBinaryTreeMaxDepth) {
            $max_depth = TestBinaryTree::query()->where('turning_point_id', $turning_point_id)->max('depth');
            $testBinaryTreeMaxDepth = TestBinaryTreeMaxDepth::query()->create([
                'id' => $turning_point_id,
                'max_depth' => $max_depth,
                'leg' => $parent->leg_of_parent,
            ]);
        }
        return $testBinaryTreeMaxDepth->max_depth;
    }
}
