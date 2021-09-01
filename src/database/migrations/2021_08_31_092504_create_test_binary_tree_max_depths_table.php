<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestBinaryTreeMaxDepthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_binary_tree_max_depths', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('test_binary_trees.turning_point_id');
            $table->unsignedInteger('max_depth')->index()->comment('最大深度');
            $table->enum('leg', ['L', 'R'])->comment('边');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `test_binary_tree_max_depths` comment '测试树状图点边最大深度表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_binary_tree_max_depths');
    }
}
