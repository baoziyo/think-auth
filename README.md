# think-auth

## 安装

### 一、执行命令安装
```
composer require baoziyo/think-auth
```
## 配置
### 公共配置
```
// auth配置
'auth'  => [
    'auth_on' => 1, // 权限开关
    'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
    'auth_group' => 'auth_group', // 用户组数据表名
    'auth_rule' => 'auth_rule', // 权限规则表
    'auth_user' => 'admin', // 用户信息表
    'menu' =>'menu'
],
```

## 使用
判断权限方法
```
// 引入类库
use think\auth\Auth;

// 获取auth实例
$auth = Auth::instance();

// 检测权限
if($auth->check('show_button',1)){// 第一个参数是规则名称,第二个参数是用户UID
	//有显示操作按钮的权限
}else{
	//没有显示操作按钮的权限
}
```

Auth类也可以对节点进行认证，我们只要将规则名称，定义为节点名称就行了。 
可以在公共控制器Base中定义_initialize方法
```
<?php
use think\Controller;
use think\auth\Auth;
class Base extends Controller
{
    public function _initialize()
	{
		$controller = request()->controller();
		$action = request()->action();
		$auth = new Auth();
		if(!$auth->check($controller . '-' . $action, session('uid'))){
			$this->error('你没有权限访问');
		}
    }
 }
```
这时候我们可以在数据库中添加的节点规则， 格式为： “控制器名称-方法名称”

Auth 类 还可以多个规则一起认证 如： 
```
$auth->check('rule1,rule2',uid); 
```
表示 认证用户只要有rule1的权限或rule2的权限，只要有一个规则的权限，认证返回结果就为true 即认证通过。 默认多个权限的关系是 “or” 关系，也就是说多个权限中，只要有个权限通过则通过。 我们也可以定义为 “and” 关系
```
$auth->check('rule1,rule2',uid,'and'); 
```
第三个参数指定为"and" 表示多个规则以and关系进行认证， 这时候多个规则同时通过认证才有权限。只要一个规则没有权限则就会返回false。

Auth认证，一个用户可以属于多个用户组。 比如我们对 show_button这个规则进行认证， 用户A 同时属于 用户组1 和用户组2 两个用户组 ， 用户组1 没有show_button 规则权限， 但如果用户组2 有show_button 规则权限，则一样会权限认证通过。 
```
$auth->getGroups(uid)
```
通过上面代码，可以获得用户所属的所有用户组，方便我们在网站上面显示。

Auth类还可以按用户属性进行判断权限， 比如 
按照用户积分进行判断， 假设我们的用户表 (think_members) 有字段 score 记录了用户积分。 
我在规则表添加规则时，定义规则表的condition 字段，condition字段是规则条件，默认为空 表示没有附加条件，用户组中只有规则 就通过认证。
如果定义了 condition字段，用户组中有规则不一定能通过认证，程序还会判断是否满足附加条件。
比如我们添加几条规则： 

> `name`字段：grade1 `condition`字段：{score}<100 <br/>
> `name`字段：grade2 `condition`字段：{score}>100 and {score}<200<br/>
> `name`字段：grade3 `condition`字段：{score}>200 and {score}<300

这里 `{score}` 表示 `think_members` 表 中字段 `score` 的值。 

那么这时候 

> $auth->check('grade1', uid) 是判断用户积分是不是0-100<br/>
> $auth->check('grade2', uid) 判断用户积分是不是在100-200<br/>
> $auth->check('grade3', uid) 判断用户积分是不是在200-300

