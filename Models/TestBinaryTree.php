<?php

namespace Azhida\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestBinaryTree extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', // 主键ID
        'parent_id', // 父级ID
        'turning_point_id', // 转折点ID
        'depth', // 深度，从0开始
        'leg_of_parent', // 节点相对于父节点的位置，取值 L | R
        'add_enable', // 节点下面是否可以添加新节点【每个点下面可以添加左右两个节点】
        'L_add_enable', // 是否可以添加左下方子节点
        'R_add_enable', // 是否可以添加右下方子节点
        'L_son_id', // 左下方节点ID
        'R_son_id', // 右下方节点ID
        'xy', // 坐标表示集 xy
        'top_xy', // 坐标表示集 xy
        'top_ids', // 坐标表示集 xy
        'full_path_start_id', // 坐标表示集 xy
        'full_path', // 坐标表示集 xy
        'guided_path', // 坐标表示集 xy
        'show_info', // 展示信息【自定义】
    ];

    protected $casts = [
        'add_enable' => 'boolean',
        'L_add_enable' => 'boolean',
        'R_add_enable' => 'boolean',
        'xy' => 'json',
        'top_xy' => 'json',
        'top_ids' => 'json',
        'show_info' => 'json',
    ];

    protected $appends = [
        'L_turning_point_id',
        'R_turning_point_id',
    ];

    public function getLTurningPointIdAttribute()
    {
        if (isset($this->attributes['leg_of_parent']) && in_array($this->attributes['leg_of_parent'], ['L', 'R'])) {
            return $this->attributes['leg_of_parent'] == 'L' ? $this->attributes['turning_point_id'] : $this->attributes['id'];
        } else {
            return '';
        }
    }

    public function getRTurningPointIdAttribute()
    {
        if (isset($this->attributes['leg_of_parent']) && in_array($this->attributes['leg_of_parent'], ['L', 'R'])) {
            return $this->attributes['leg_of_parent'] == 'R' ? $this->attributes['turning_point_id'] : $this->attributes['id'];
        } else {
            return '';
        }
    }

}
