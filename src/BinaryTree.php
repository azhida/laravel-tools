<?php

namespace Azhida\Tools;

// 这是一个标准二叉树结构
class BinaryTree
{
    // 父级链路的长度，默认 100，即 当 父级的深度depth 是 $full_path_long的整数倍时，full_path 字段 从 父级ID重新开始
    public static $full_path_long = 100;

    public static $nodes = [];
    
    public $table = 'test_binary_trees';

    // 一个点的字段标记
    public $fields = [
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

    public function createTables()
    {
        
    }

    // 横向添加子节点 -- 填满指定ID的指定层数
    public static function addNodes_x($id, $depth = 10)
    {

    }

    // 纵向添加子节点 -- 填满指定ID的指定边
    public static function addNodes_y($id, $leg, $depth = 1, $start_time = '')
    {
        if (!$start_time) $start_time = time();




    }

    // 添加第一个节点
    public static function addNode_first($leg_of_parent = 'L', $show_info = [])
    {
        $id = 1;
        $node = [
            'id' => $id, // 主键ID
            'parent_id' => 0, // 父级ID
            'turning_point_id' => 0, // 转折点ID
            'depth' => 0, // 深度，从0开始
            'leg_of_parent' => $leg_of_parent, // 节点相对于父节点的位置，取值 L | R
            'add_enable' => true, // 节点下面是否可以添加新节点【每个点下面可以添加左右两个节点】
            'L_add_enable' => true, // 是否可以添加左下方子节点
            'R_add_enable' => true, // 是否可以添加右下方子节点
            'L_son_id' => 0, // 左下方节点ID
            'R_son_id' => 0, // 右下方节点ID
            'xy' => self::initXY(), // 坐标表示集 xy
            'top_xy' => self::initXY(), // 坐标表示集 xy
            'top_ids' => self::initTopIds($id), // 该节点的顶点Id集合
            'full_path_start_id' => $id, // full_path 的第一个ID
            'full_path' => "-{$id}-", // 节点所有父级ID链路，每当 父级depth 的 整百倍数【整千倍数】时，重新开始，目的是减少长度，减少数据的物理大小
            'guided_path' => '-', // full_path 的扩展
            'show_info' => $show_info, // 展示信息【自定义】
        ];
        self::$nodes[$id] = $node;

        return $node;
    }

    // 获取子节点
    public static function addNode(array $parent, $leg_of_parent, $show_info = [])
    {
        if (empty($parent)) return [];

        $id = max(array_column(self::$nodes, 'id')) + 1;
        $full_path_start_id = $parent['full_path_start_id'];
        $full_path = $parent['full_path'] . $parent['id'] . '-';
        $guided_path = $parent['guided_path'] . "{$parent['id']}:{$leg_of_parent}-";
        if ($parent['depth'] % self::$full_path_long == 0) {
            $full_path_start_id = $parent['id'];
            $full_path = "-{$parent['id']}-";
            $guided_path = "-{$parent['id']}:{$leg_of_parent}-";
        }
        $node = [
            'id' => $id, // 主键ID，理应自增
            'parent_id' => $parent['id'], // 父级ID
            'turning_point_id' => $leg_of_parent == $parent['leg_of_parent'] ? $parent['turning_point_id'] : $parent['id'], // 转折点ID
            'depth' => $parent['depth'] + 1, // 深度，从0开始
            'leg_of_parent' => $leg_of_parent, // 节点相对于父节点的位置，取值 L | R
            'add_enable' => true, // 节点下面是否可以添加新节点【每个点下面可以添加左右两个节点】
            'L_add_enable' => true, // 是否可以添加左下方子节点
            'R_add_enable' => true, // 是否可以添加右下方子节点
            'L_son_id' => 0, // 左下方节点ID
            'R_son_id' => 0, // 右下方节点ID
            'xy' => self::makeXY($parent['xy'], $leg_of_parent), // 坐标表示集 xy
            'top_xy' => self::makeTopXY($parent, $leg_of_parent), // 坐标表示集 xy
            'top_ids' => self::makeTopIds($parent), // 该节点的顶点Id集合
            'full_path_start_id' => $full_path_start_id, // full_path 的第一个ID
            'full_path' => $full_path, // 节点所有父级ID链路，每当 父级depth 的 整百倍数【整千倍数】时，重新开始，目的是减少长度，减少数据的物理大小
            'guided_path' => $guided_path, // full_path 的扩展
            'show_info' => $show_info, // 展示信息【自定义】
        ];
        self::$nodes[$id] = $node;

        // 更新父节点
        $add_enable_str = $leg_of_parent . '_add_enable';
        $parent[$add_enable_str] = false;
        if (!$parent['L_add_enable'] && !$parent['R_add_enable']) {
            $parent['add_enable'] = false;
        }
        $son_id_str = $leg_of_parent . '_son_id';
        $parent[$son_id_str] = $id;

        self::$nodes[$parent['id']] = $parent;

        return $node;
    }

    public static function initXY()
    {
        return [
            'depth' => 0,
            'depth_x_1' => 0,
            'depth_x_10' => 0,
            'depth_x_100' => 0,
            'depth_x_1000' => 0,
            'depth_x_10000' => 0,
            'depth_x_100000' => 0,
            'depth_x_1000000' => 0,
            'depth_x_10000000' => 0,
            'depth_y_1' => 0,
            'depth_y_10' => 0,
            'depth_y_100' => 0,
            'depth_y_1000' => 0,
            'depth_y_10000' => 0,
            'depth_y_100000' => 0,
            'depth_y_1000000' => 0,
            'depth_y_10000000' => 0,
        ];
    }

    public static function initTopIds($id)
    {
        return [
            'depth' => 0,
            'depth_10' => $id,
            'depth_100' => $id,
            'depth_1000' => $id,
            'depth_10000' => $id,
            'depth_100000' => $id,
            'depth_1000000' => $id,
            'depth_10000000' => $id,
        ];
    }

    public static function makeXY($parent_xy, $leg)
    {
        $depth = $parent_xy['depth'] + 1; // 绝对深度
        $xy = $parent_xy;
        $xy['depth'] = $depth;

        // depth_1
        $xy['depth_x_1'] = $parent_xy['depth_x_1'] * 2 + ($leg == 'R' ? 1 : 0);
        $xy['depth_y_1'] = $parent_xy['depth_y_1'] + 1;

        // depth_10
        if ($depth % 10 == 0) {
            $xy['depth_x_10'] = $parent_xy['depth_x_10'] * 2;
            $xy['depth_y_10'] = $parent_xy['depth_y_10'] + 1;
            if ($xy['depth_x_1'] / pow(2, 10) >= 0.5) $xy['depth_x_10'] += 1;
            $xy['depth_x_1'] = $xy['depth_y_1'] = 0;
        }

        // depth_100
        if ($depth % 100 == 0) {
            $xy['depth_x_100'] = $parent_xy['depth_x_100'] * 2;
            $xy['depth_y_100'] = $parent_xy['depth_y_100'] + 1;
            if ($xy['depth_x_10'] / pow(2, 10) >= 0.5) $xy['depth_x_100'] += 1;
            $xy['depth_x_10'] = $xy['depth_y_10'] = 0;
        }

        // depth_1000
        if ($depth % 1000 == 0) {
            $xy['depth_x_1000'] = $parent_xy['depth_x_1000'] * 2;
            $xy['depth_y_1000'] = $parent_xy['depth_y_1000'] + 1;
            if ($xy['depth_x_100'] / pow(2, 10) >= 0.5) $xy['depth_x_1000'] += 1;
            $xy['depth_x_100'] = $xy['depth_y_100'] = 0;
        }

        // depth_10000
        if ($depth % 10000 == 0) {
            $xy['depth_x_10000'] = $parent_xy['depth_x_10000'] * 2;
            $xy['depth_y_10000'] = $parent_xy['depth_y_10000'] + 1;
            if ($xy['depth_x_1000'] / pow(2, 10) >= 0.5) $xy['depth_x_10000'] += 1;
            $xy['depth_x_1000'] = $xy['depth_y_1000'] = 0;
        }

        // depth_100000
        if ($depth % 100000 == 0) {
            $xy['depth_x_100000'] = $parent_xy['depth_x_100000'] * 2;
            $xy['depth_y_100000'] = $parent_xy['depth_y_100000'] + 1;
            if ($xy['depth_x_10000'] / pow(2, 10) >= 0.5) $xy['depth_x_100000'] += 1;
            $xy['depth_x_10000'] = $xy['depth_y_10000'] = 0;
        }

        // depth_1000000
        if ($depth % 1000000 == 0) {
            $xy['depth_x_1000000'] = $parent_xy['depth_x_1000000'] * 2;
            $xy['depth_y_1000000'] = $parent_xy['depth_y_1000000'] + 1;
            if ($xy['depth_x_100000'] / pow(2, 10) >= 0.5) $xy['depth_x_1000000'] += 1;
            $xy['depth_x_100000'] = $xy['depth_y_100000'] = 0;
        }

        // depth_10000000
        if ($depth % 10000000 == 0) {
            $xy['depth_x_10000000'] = $parent_xy['depth_x_10000000'] * 2;
            $xy['depth_y_10000000'] = $parent_xy['depth_y_10000000'] + 1;
            if ($xy['depth_x_1000000'] / pow(2, 10) >= 0.5) $xy['depth_x_10000000'] += 1;
            $xy['depth_x_1000000'] = $xy['depth_y_1000000'] = 0;
        }

        return $xy;
    }

    public static function makeTopXY($parent, $leg)
    {
        $depth = $parent['xy']['depth'] + 1; // 绝对深度

        if ($depth % 10 != 0) return  $parent['top_xy'];

        $parent_xy = $parent['xy'];
        $xy = $parent_xy;
        $xy['depth'] = $depth;

        // depth_1
        $xy['depth_x_1'] = $parent_xy['depth_x_1'] * 2 + ($leg == 'R' ? 1 : 0);
        $xy['depth_y_1'] = $parent_xy['depth_y_1'] + 1;

        // depth_10
        if ($depth % 10 == 0) {
            $xy['depth_x_10'] = $parent_xy['depth_x_10'] * 2;
            $xy['depth_y_10'] = $parent_xy['depth_y_10'] + 1;
            if ($xy['depth_x_1'] / pow(2, 10) >= 0.5) $xy['depth_x_10'] += 1;
        }

        // depth_100
        if ($depth % 100 == 0) {
            $xy['depth_x_100'] = $parent_xy['depth_x_100'] * 2;
            $xy['depth_y_100'] = $parent_xy['depth_y_100'] + 1;
            if ($xy['depth_x_10'] / pow(2, 10) >= 0.5) $xy['depth_x_100'] += 1;
        }

        // depth_1000
        if ($depth % 1000 == 0) {
            $xy['depth_x_1000'] = $parent_xy['depth_x_1000'] * 2;
            $xy['depth_y_1000'] = $parent_xy['depth_y_1000'] + 1;
            if ($xy['depth_x_100'] / pow(2, 10) >= 0.5) $xy['depth_x_1000'] += 1;
        }

        // depth_10000
        if ($depth % 10000 == 0) {
            $xy['depth_x_10000'] = $parent_xy['depth_x_10000'] * 2;
            $xy['depth_y_10000'] = $parent_xy['depth_y_10000'] + 1;
            if ($xy['depth_x_1000'] / pow(2, 10) >= 0.5) $xy['depth_x_10000'] += 1;
        }

        // depth_100000
        if ($depth % 100000 == 0) {
            $xy['depth_x_100000'] = $parent_xy['depth_x_100000'] * 2;
            $xy['depth_y_100000'] = $parent_xy['depth_y_100000'] + 1;
            if ($xy['depth_x_10000'] / pow(2, 10) >= 0.5) $xy['depth_x_100000'] += 1;
        }

        // depth_1000000
        if ($depth % 1000000 == 0) {
            $xy['depth_x_1000000'] = $parent_xy['depth_x_1000000'] * 2;
            $xy['depth_y_1000000'] = $parent_xy['depth_y_1000000'] + 1;
            if ($xy['depth_x_100000'] / pow(2, 10) >= 0.5) $xy['depth_x_1000000'] += 1;
        }

        // depth_10000000
        if ($depth % 10000000 == 0) {
            $xy['depth_x_10000000'] = $parent_xy['depth_x_10000000'] * 2;
            $xy['depth_y_10000000'] = $parent_xy['depth_y_10000000'] + 1;
            if ($xy['depth_x_1000000'] / pow(2, 10) >= 0.5) $xy['depth_x_10000000'] += 1;
        }

        return $xy;
    }

    public static function makeTopIds($parent)
    {
        $parent_id = $parent['id'];
        $depth = $parent['depth'];

        $top_ids = $parent->top_ids;
        if ($depth % 10 == 0) $top_ids['depth_10'] = $parent_id;
        if ($depth % 100 == 0) $top_ids['depth_100'] = $parent_id;
        if ($depth % 1000 == 0) $top_ids['depth_1000'] = $parent_id;
        if ($depth % 10000 == 0) $top_ids['depth_10000'] = $parent_id;
        if ($depth % 100000 == 0) $top_ids['depth_100000'] = $parent_id;
        if ($depth % 1000000 == 0) $top_ids['depth_1000000'] = $parent_id;
        if ($depth % 10000000 == 0) $top_ids['depth_10000000'] = $parent_id;
        $top_ids['depth'] = $depth + 1;
        return $top_ids;
    }
}