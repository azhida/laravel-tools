<h1 align="center"> laravel-tools </h1>

<p align="center"> 收集整理常用方法.</p>


## Installing

```shell
$ composer require azhida/laravel-tools -vvv
```
###### 发布迁移文件
```shell
$ php artisan vendor:publish
# 选择 Azhida\Tools\ServiceProvider
```

## Usage

#### 常用方法：
- Tool::arrayToTree() 一维数组转树形结构

#### 封装几个标准二叉树的操作方法：
###### 添加节点
```
// 添加一个节点
TestBinaryTree::addNode_first()

// 添加单个子节点
TestBinaryTree::addNode(TestBinaryTree $parent, $leg_of_parent, $show_info = []);

// 批量添加子节点 -- 指定节点指定边
TestBinaryTree::addNodes_y($id, $leg, $depth = 1, $start_time = '');

// 批量添加子节点 -- 从指定节点下一层开始添加，添加顺序为 depth asc ，id asc
TestBinaryTree::addNodes_x($id, $depth = 10);
```
###### 获取节点
```
// 获取ID节点下可添加的点位 -- 自上而下，从左到右
TestBinaryTree::searchAddEnableNodeById_DepthAsc_LToR($id);

// 获取ID节点下可添加的点位 -- 左腿【右腿】最底部
TestBinaryTree::searchAddEnableNodeById_Leg_MaxDepth($id, $leg = 'L');

// 获取ID的所有上级
TestBinaryTree::getParentsById($id);

// 获取ID所有子节点
TestBinaryTree::getSonsById($id, $depth = 10);

// 判断 A、D 两个节点是否存在祖孙关系
TestBinaryTree::isAncestor_AD($ancestor_id, $descendant_id);
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/azhida/laravel-tools/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/azhida/laravel-tools/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT