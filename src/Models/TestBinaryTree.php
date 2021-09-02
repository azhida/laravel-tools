<?php

namespace Azhida\Tools\Models;

use Azhida\Tools\Models\TestBinaryTreeMaxDepth;
use Azhida\Tools\Tool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestBinaryTree extends Model
{
    use HasFactory;

    // 父级链路的长度，默认 100，即 当 父级的深度depth 是 $full_path_long的整数倍时，full_path 字段 从 父级ID重新开始
    public static $full_path_long = 100;
    public static $num = 0;

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

    // 获取ID节点下可添加的点位 -- 自上而下，从左到右
    public static function searchAddEnableNodeById_DepthAsc_LToR($id)
    {
        $parent = TestBinaryTree::query()->where('add_enable', true)->find($id);
        if (!$parent) {

            $parent = TestBinaryTree::query()
                ->where('add_enable', true)
                ->where('full_path', 'like', "-{$id}-")
                ->orderBy('depth')
                ->orderBy('v_top_depth_x_10000000')
                ->orderBy('v_top_depth_x_1000000')
                ->orderBy('v_top_depth_x_100000')
                ->orderBy('v_top_depth_x_10000')
                ->orderBy('v_top_depth_x_1000')
                ->orderBy('v_top_depth_x_100')
                ->orderBy('v_top_depth_x_10')
                ->orderBy('v_top_depth_x_1')
                ->orderBy('v_depth_x_1')
                ->first();

            if (!$parent) {

                $sons = TestBinaryTree::query()->where('full_path', 'like', "-{$id}-")->get();
                $current_max_depth = $sons->max('depth');
                $last_ids = $sons->where('depth', $current_max_depth)->pluck('id')->toArray();

                while (count($sons) > 0) {

                    $query_1 = TestBinaryTree::query();
                    if ($current_max_depth % 10000000 == 0) {
                        $query_1->whereIn('v_top_ids_depth_10000000', $last_ids);
                    } else if ($current_max_depth % 1000000 == 0) {
                        $query_1->whereIn('v_top_ids_depth_1000000', $last_ids);
                    } else if ($current_max_depth % 100000 == 0) {
                        $query_1->whereIn('v_top_ids_depth_100000', $last_ids);
                    } else if ($current_max_depth % 10000 == 0) {
                        $query_1->whereIn('v_top_ids_depth_10000', $last_ids);
                    } else if ($current_max_depth % 1000 == 0) {
                        $query_1->whereIn('v_top_ids_depth_1000', $last_ids);
                    } else {
                        $query_1->whereIn('v_top_ids_depth_100', $last_ids);
                    }
                    $parent = $query_1
                        ->where('add_enable', true)
                        ->where('depth', '>', $current_max_depth)
                        ->orderBy('depth')
                        ->orderBy('v_top_depth_x_10000000')
                        ->orderBy('v_top_depth_x_1000000')
                        ->orderBy('v_top_depth_x_100000')
                        ->orderBy('v_top_depth_x_10000')
                        ->orderBy('v_top_depth_x_1000')
                        ->orderBy('v_top_depth_x_100')
                        ->orderBy('v_top_depth_x_10')
                        ->orderBy('v_top_depth_x_1')
                        ->orderBy('v_depth_x_1')
                        ->first();
                    if ($parent) break;

                    $query = TestBinaryTree::query()->where('depth', '>', $current_max_depth);
                    if ($current_max_depth % 10000000 == 0) {
                        $query->whereIn('v_top_ids_depth_10000000', $last_ids);
                    } else if ($current_max_depth % 1000000 == 0) {
                        $query->whereIn('v_top_ids_depth_1000000', $last_ids);
                    } else if ($current_max_depth % 100000 == 0) {
                        $query->whereIn('v_top_ids_depth_100000', $last_ids);
                    } else if ($current_max_depth % 10000 == 0) {
                        $query->whereIn('v_top_ids_depth_10000', $last_ids);
                    } else if ($current_max_depth % 1000 == 0) {
                        $query->whereIn('v_top_ids_depth_1000', $last_ids);
                    } else {
                        $query->whereIn('v_top_ids_depth_100', $last_ids);
                    }
                    $sons = $query->get();
                    if (count($sons) == 0) break;

                    $current_max_depth = $sons->max('depth');
                    $last_ids = $sons->where('depth', $current_max_depth)->pluck('id')->toArray();

                }

            }

        }

        return $parent;
    }

    // 获取ID节点下可添加的点位 -- 左腿【右腿】最底部
    public static function searchAddEnableNodeById_Leg_MaxDepth($id, $leg = 'L')
    {
        $node = TestBinaryTree::query()->find($id);
        if (!$node) return null;
        if ($node->{$leg.'_add_enable'}) return $node;

        $max_depth = TestBinaryTreeMaxDepth::getTestBinaryTreeMaxDepth($node->{$leg.'_turning_point_id'});
        $node = TestBinaryTree::query()
            ->where('turning_point_id', $node->{$leg.'_turning_point_id'})
            ->where('depth', $max_depth)
            ->first();
        return $node;
    }

    // 判断 A、D 两个节点是否存在祖孙关系
    public static function isAncestor_AD($ancestor_id, $descendant_id): bool
    {
        if ($ancestor_id == $descendant_id) return false;

        $testBinaryTrees = TestBinaryTree::query()->whereIn('id', [$ancestor_id, $descendant_id])->get();
        if (count($testBinaryTrees) != 2) return false;

        $ancestor = null;
        $descendant = null;
        foreach ($testBinaryTrees as $testBinaryTree) {
            if ($testBinaryTree->id == $ancestor_id) $ancestor = $testBinaryTree;
            if ($testBinaryTree->id == $descendant_id) $descendant = $testBinaryTree;
        }
        if (!$ancestor || !$descendant || $ancestor->depth >= $descendant->depth) return false;

        while ($descendant && $descendant->parent_id != 0) {
            $ids = explode('-', $descendant->full_path);
            if (in_array($ancestor_id, $ids)) return  true;
            $descendant = TestBinaryTree::query()->find($descendant->full_path_start_id);
        }
        return false;
    }

    // 获取ID的所有上级
    public static function getParentsById($id)
    {
        $testBinaryTree = TestBinaryTree::query()->find($id);
        if (!$testBinaryTree) return Tool::resFailMsg('ID错误');
        $full_path = '';
        while ($testBinaryTree && $testBinaryTree->parent_id != 0) {
            $full_path .= $testBinaryTree->full_path;
            $testBinaryTree = TestBinaryTree::query()->find($testBinaryTree->full_path_start_id);
        }

        $ids = explode('-', $full_path);
        $parents = TestBinaryTree::query()->whereIn('id', $ids)->get();
        $data = [
            'parents' => $parents,
            'full_path' => $full_path,
        ];
        return Tool::resSuccessMsg('', $data);
    }

    // 获取ID所有子节点
    public static function getSonsById($id, $depth = 10)
    {
        $testBinaryTree = TestBinaryTree::query()->find($id);
        $max_depth = $testBinaryTree->depth + $depth;

        $select_fields = [
            'id',
            'parent_id',
            'depth',
        ];

        $sons = TestBinaryTree::query()
            ->select($select_fields)
            ->where('depth', '>', $testBinaryTree->depth)
            ->where('depth', '<=', $max_depth)
            ->where('full_path', 'like', "%-{$id}-%")
            ->orWhere('id', $id)
            ->get();

        $current_max_depth = $sons->max('depth');
        $last_ids = $sons->where('depth', $current_max_depth)->pluck('id')->toArray();

        while ($current_max_depth < $max_depth && $current_max_depth % 100 == 0) {

            $query = TestBinaryTree::query()->select($select_fields)
                ->where('depth', '>', $current_max_depth)
                ->where('depth', '<=', $max_depth);
            if ($current_max_depth % 10000000 == 0) {
                $query->whereIn('v_top_ids_depth_10000000', $last_ids);
            }
            else if ($current_max_depth % 1000000 == 0) {
                $query->whereIn('v_top_ids_depth_1000000', $last_ids);
            }
            else if ($current_max_depth % 100000 == 0) {
                $query->whereIn('v_top_ids_depth_100000', $last_ids);
            }
            else if ($current_max_depth % 10000 == 0) {
                $query->whereIn('v_top_ids_depth_10000', $last_ids);
            }
            else if ($current_max_depth % 1000 == 0) {
                $query->whereIn('v_top_ids_depth_1000', $last_ids);
            }
            else {
                $query->whereIn('v_top_ids_depth_100', $last_ids);
            }
            $sons_1 = $query->get();
            $sons = $sons->merge($sons_1);

            $current_max_depth = $sons->max('depth');
            $last_ids = $sons->where('depth', $current_max_depth)->pluck('id')->toArray();

        }

        $meta = [
            'id' => $id,
            'depth' => $depth,
            'max_depth' => $max_depth,
            'total_count' => count($sons),
        ];
        return Tool::resSuccessMsg('', $sons, $meta);
    }

    // 横向添加子节点 -- 填满指定ID的指定层数
    public static function addNodes_x($id, $depth = 10)
    {
        $start_time = time();

        TestBinaryTree::addNodes_y($id, 'L', $depth, $start_time);
        TestBinaryTree::addNodes_y($id, 'R', $depth, $start_time);

        $depth_num = 0;

        $testBinaryTree = TestBinaryTree::query()->find($id);
        $current_depth = $testBinaryTree->depth + 1;

        $limit = 100;

        while ($current_depth < $depth) {
            $msg = [
                '$depth_num' => $depth_num,
                '$current_depth' => $current_depth,
            ];
            echo Tool::loggerCustom(__CLASS__, __FUNCTION__, 'x轴添加中[0]', $msg, true);

            $start_id = 0;
            $testBinaryTrees = TestBinaryTree::query()
                ->where('depth', '=', $current_depth)
                ->where('add_enable', true)
                ->orderBy('id')->limit($limit)->get();
            while (count($testBinaryTrees) > 0) {
                $msg = [
                    '$depth_num' => $depth_num,
                    '$current_depth' => $current_depth,
                    '$start_id' => $start_id
                ];
                echo Tool::loggerCustom(__CLASS__, __FUNCTION__, 'x轴添加中[1]', $msg, true);

                foreach ($testBinaryTrees as $item) {
                    $start_id = $item->id;

                    if ($item->L_add_enable) {
                        TestBinaryTree::addNodes_y($item->id, 'L', $depth, $start_time);
                    }
                    if ($item->R_add_enable) {
                        TestBinaryTree::addNodes_y($item->id, 'R', $depth, $start_time);
                    }

                }

                $testBinaryTrees = TestBinaryTree::query()
                    ->where('depth', '=', $current_depth)
                    ->where('add_enable', true)
                    ->where('id', '>', $start_id)
                    ->orderBy('id')->limit($limit)->get();
            }

            $depth_num++;
            $current_depth++;
        }

        $msg = [
            '$depth_num' => $depth_num,
            '$current_depth' => $current_depth,
            '$depth' => $depth,
        ];
        echo Tool::loggerCustom(__CLASS__, __FUNCTION__, 'x轴添加结束', $msg, true);

        return '结束';
    }

    // 纵向添加子节点 -- 填满指定ID的指定边
    public static function addNodes_y($id, $leg, $depth = 1, $start_time = '')
    {
        if (!$start_time) $start_time = time();

        $parent = TestBinaryTree::query()->find($id);
        if (!$parent->{$leg.'_add_enable'}) {
            $turning_point_id = $parent->{$leg.'_turning_point_id'};
            $max_depth = TestBinaryTreeMaxDepth::getTestBinaryTreeMaxDepth($turning_point_id);

            if ($max_depth > $depth) return Tool::resFailMsg('已经添加，无需重复');
            $parent = TestBinaryTree::query()
                ->where('turning_point_id', $turning_point_id)
                ->where('leg_of_parent', $leg)
                ->where('depth', $max_depth)
                ->first();

        }

        $used_time = 0;
        $average_time = 0;
        while ($parent->depth < $depth) {

            self::$num++;
            $used_time = time() - $start_time;
            $average_time = $used_time / self::$num;
            $echo_msg = [
                '$num' => self::$num,
                '$depth' => $parent->depth,
                '$used_time' => $used_time,
                '$average_time' => $average_time,
            ];
            echo Tool::loggerCustom(__CLASS__, __FUNCTION__, 'Y添加子节点-ing', $echo_msg, 1);

            $parent = TestBinaryTree::addNode($parent, $leg);
            if (!$parent) dd('添加子节点失败');
        }

        $echo_msg = [
            '$id' => $id,
            '$leg' => $leg,
            '$depth' => $depth,
            '$used_time' => $used_time,
            '$average_time' => $average_time,
        ];
        echo Tool::loggerCustom(__CLASS__, __FUNCTION__, 'Y添加子节点-end', $echo_msg, 1);
        return Tool::resSuccessMsg('子节点添加结束，' . json_encode($echo_msg, JSON_UNESCAPED_UNICODE));
    }

    // 添加首节点
    public static function addNode_first($leg_of_parent = 'L', $show_info = [])
    {
        if (!$node = TestBinaryTree::query()->where('parent_id', 0)->first()) {
            $id = 1;
            $node_insert_data = [
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
            $node = TestBinaryTree::query()->create($node_insert_data);
        }
        return $node;
    }

    // 添加子节点
    public static function addNode(TestBinaryTree $parent, $leg_of_parent, $show_info = [])
    {
        if (!$parent) return null;
        if (!$parent->{$leg_of_parent.'_add_enable'}) return null;

        $turning_point_id = $parent->{$leg_of_parent.'_turning_point_id'};
        $full_path_start_id = $parent->full_path_start_id;
        $full_path = $parent->full_path . $parent->id . '-';
        $guided_path = $parent->guided_path . "{$parent->id}:{$leg_of_parent}-";
        if ($parent->depth % self::$full_path_long == 0) {
            $full_path_start_id = $parent->id;
            $full_path = "-{$parent->id}-";
            $guided_path = "-{$parent->id}:{$leg_of_parent}-";
        }
        $node_insert_data = [
            'parent_id' => $parent->id, // 父级ID
            'turning_point_id' => $turning_point_id, // 转折点ID
            'depth' => $parent->depth + 1, // 深度，从0开始
            'leg_of_parent' => $leg_of_parent, // 节点相对于父节点的位置，取值 L | R
            'add_enable' => true, // 节点下面是否可以添加新节点【每个点下面可以添加左右两个节点】
            'L_add_enable' => true, // 是否可以添加左下方子节点
            'R_add_enable' => true, // 是否可以添加右下方子节点
            'L_son_id' => 0, // 左下方节点ID
            'R_son_id' => 0, // 右下方节点ID
            'xy' => self::makeXY($parent->xy, $leg_of_parent), // 坐标表示集 xy
            'top_xy' => self::makeTopXY($parent, $leg_of_parent), // 坐标表示集 xy
            'top_ids' => self::makeTopIds($parent), // 该节点的顶点Id集合
            'full_path_start_id' => $full_path_start_id, // full_path 的第一个ID
            'full_path' => $full_path, // 节点所有父级ID链路，每当 父级depth 的 整百倍数【整千倍数】时，重新开始，目的是减少长度，减少数据的物理大小
            'guided_path' => $guided_path, // full_path 的扩展
            'show_info' => $show_info, // 展示信息【自定义】
        ];
        $node = TestBinaryTree::query()->create($node_insert_data);

        $testBinaryTreeMaxDepth = TestBinaryTreeMaxDepth::query()->find($turning_point_id);
        if ($testBinaryTreeMaxDepth) {
            $testBinaryTreeMaxDepth->update(['max_depth' => $node->depth]);
        } else {
            TestBinaryTreeMaxDepth::query()->create([
                'id' => $turning_point_id,
                'max_depth' => $node->depth,
                'leg' => $node->leg_of_parent,
            ]);
        }

        // 更新父节点
        $parent->{$leg_of_parent.'_add_enable'} = false;
        if (!$parent->L_add_enable && !$parent->R_add_enable) {
            $parent->add_enable = false;
        }
        $parent->{$leg_of_parent.'_son_id'} = $node->id;
        $parent->save();

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

    public static function makeTopXY(TestBinaryTree $parent, $leg)
    {
        $depth = $parent->depth + 1; // 绝对深度

        if ($depth % 10 != 0) return  $parent['top_xy'];

        $parent_xy = $parent->xy;
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

    public static function makeTopIds(TestBinaryTree $parent)
    {
        $parent_id = $parent->id;
        $depth = $parent->depth;

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
