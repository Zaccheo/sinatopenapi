# 新浪微博开放平台调用类使用示例 #
## 适用于PHP5及以上版本及安装curl扩展库 ##
## 使用HTTP普通鉴权(Basic Authentication)方式 ##

使用示例：
```
header("Content-type:text/html;charset=utf-8");
require('SinaOpenApi.php');
$openapi = new SinaOpenApi('App Key');
$openapi->setUser('微博登录账户', '密码');

//上传图片测试
$upload_params = array(
	'status'=>'This is a upload file',
	'pic'=>'E:\Images\animal\01.jpg',
	'mime_type'=>'image/jpg',
	'filename'=>'01.jpg',
);
//开放平台接口
$urls = array(
//url:请求地址，params:请求参数(参照接口说明)，method:请求方式，目前只支持GET和POST方法
//	array('url'=>'statuses/public_timeline','params'=>array('page'=>1, 'count'=>5), 'method'=>'GET'),
//	array('url'=>'statuses/friends_timeline','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/user_timeline','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/mentions','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/comments_timeline','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/comments_by_me','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/comments','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/counts','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/unread','params'=>array(), 'method'=>'GET'),
//	array('url'=>'statuses/show/123456','params'=>array('id'=>'123456'), 'method'=>'GET'),
//	array('url'=>'statuses/update','params'=>array('status'=>'This is a test blog!'), 'method'=>'POST'),
//	array('url'=>'statuses/upload','params'=>$upload_params, 'method'=>'POST'),
//	array('url'=>'statuses/destroy/123456','params'=>array('id'=>123456), 'method'=>'POST'),
//	array('url'=>'statuses/repost','params'=>array('id'=>123456, 'status'=>'test repost'), 'method'=>'POST'),
//	array('url'=>'statuses/comment','params'=>array('id'=>123456, 'comment'=>'test comment'), 'method'=>'POST'),
//	array('url'=>'direct_messages','params'=>array('page'=>1, 'count'=>5), 'method'=>'GET'),
//.....
);

foreach ($urls as $url) {
	$content = $openapi->request($url['url'], $url['params'], $url['method']);
	var_dump($content);
}
?>
```