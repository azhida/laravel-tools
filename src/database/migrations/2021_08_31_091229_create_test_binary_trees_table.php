<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestBinaryTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_binary_trees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->index()->default(0)->comment('父节点ID');
            $table->unsignedBigInteger('turning_point_id')->index()->default(0)->comment('转折节点ID');
            $table->unsignedInteger('depth')->default(0)->index()->comment('绝对深度');
            $table->enum('leg_of_parent', ['L', 'R'])->comment('相对于父节点的腿');
            $table->boolean('add_enable')->default(true)->comment('是否可添加子节点');
            $table->boolean('L_add_enable')->default(true)->comment('是否可添加左下方子节点');
            $table->boolean('R_add_enable')->default(true)->comment('是否可添加右下方子节点');
            $table->unsignedBigInteger('L_son_id')->default(0)->comment('左下方子节点ID');
            $table->unsignedBigInteger('R_son_id')->default(0)->comment('右下方子节点ID');
            $table->json('xy')->nullable()->comment('坐标表示集');
            $table->json('top_xy')->nullable()->comment('顶点坐标表示集');
            $table->json('top_ids')->nullable()->comment('顶点ID集');
            $table->unsignedBigInteger('full_path_start_id')->default(0)->comment('full_path的第一个ID');
            // 节点所有父级ID链路，每当 父级depth 的 整百倍数【整千倍数】时，重新开始，目的是减少长度，减少数据的物理大小
            $table->text('full_path')->nullable()->comment('节点所有父级ID链路');
            $table->text('guided_path')->nullable()->comment('full_path 的扩展，带左右标识');
            $table->json('show_info')->nullable()->comment('展示信息【自定义】');
            $table->timestamps();

            $table->integer('v_depth_x_1')->virtualAs('xy->>"$.depth_x_1"')->index();
            $table->integer('v_depth_x_10')->virtualAs('xy->>"$.depth_x_10"')->index();
            $table->integer('v_depth_x_100')->virtualAs('xy->>"$.depth_x_100"')->index();
            $table->integer('v_depth_x_1000')->virtualAs('xy->>"$.depth_x_1000"')->index();
            $table->integer('v_depth_x_10000')->virtualAs('xy->>"$.depth_x_10000"')->index();
            $table->integer('v_depth_x_100000')->virtualAs('xy->>"$.depth_x_100000"')->index();
            $table->integer('v_depth_x_1000000')->virtualAs('xy->>"$.depth_x_1000000"')->index();
            $table->integer('v_depth_x_10000000')->virtualAs('xy->>"$.depth_x_10000000"')->index();

            $table->integer('v_top_depth_x_1')->virtualAs('top_xy->>"$.depth_x_1"')->index();
            $table->integer('v_top_depth_x_10')->virtualAs('top_xy->>"$.depth_x_10"')->index();
            $table->integer('v_top_depth_x_100')->virtualAs('top_xy->>"$.depth_x_100"')->index();
            $table->integer('v_top_depth_x_1000')->virtualAs('top_xy->>"$.depth_x_1000"')->index();
            $table->integer('v_top_depth_x_10000')->virtualAs('top_xy->>"$.depth_x_10000"')->index();
            $table->integer('v_top_depth_x_100000')->virtualAs('top_xy->>"$.depth_x_100000"')->index();
            $table->integer('v_top_depth_x_1000000')->virtualAs('top_xy->>"$.depth_x_1000000"')->index();
            $table->integer('v_top_depth_x_10000000')->virtualAs('top_xy->>"$.depth_x_10000000"')->index();

            $table->integer('v_top_ids_depth_10')->virtualAs('top_ids->>"$.depth_10"')->index();
            $table->integer('v_top_ids_depth_100')->virtualAs('top_ids->>"$.depth_100"')->index();
            $table->integer('v_top_ids_depth_1000')->virtualAs('top_ids->>"$.depth_1000"')->index();
            $table->integer('v_top_ids_depth_10000')->virtualAs('top_ids->>"$.depth_10000"')->index();
            $table->integer('v_top_ids_depth_100000')->virtualAs('top_ids->>"$.depth_100000"')->index();
            $table->integer('v_top_ids_depth_1000000')->virtualAs('top_ids->>"$.depth_1000000"')->index();
            $table->integer('v_top_ids_depth_10000000')->virtualAs('top_ids->>"$.depth_10000000"')->index();

        });
        \DB::statement("ALTER TABLE `test_binary_trees` comment '测试树状图节点信息表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_binary_trees');
    }
}
