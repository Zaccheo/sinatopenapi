<?php
/**
 * 新浪微博开放平台接口调用处理类
 * 此类只适用于Sina App Engine，PHP5环境及需要CURL扩展库支持
 * 使用HTTP普通鉴权(Basic Authentication)方式
 * 
 * @copyright http://lvyaozu.sinaapp.com/
 */
class SaeOpenApi
{
	public $curl;
	protected $_user = array();
	protected $_appKey;
	
	public function __construct($appKey, $username=null, $password=null) {
		$this->_appKey = $appKey;
		$this->curl = curl_init();
		if(!empty($username) && !empty($password)) {
			$this->setUser($username, $password);
		}
	}
	
	public function setUser($username, $password) {
		$this->_user['username'] = $username;
		$this->_user['password'] = $password;
		curl_setopt($this->curl , CURLOPT_USERPWD , "$username:$password");
	}
	
	public function request($url, $params=array(), $method='GET') {
		$apiurl = "http://api.t.sina.com.cn/";
		$apiurl .= trim($url, '/');
		$apiurl .= '.json';
		$params['source'] = $this->_appKey;
		if($url == 'statuses/upload') {
			$content = $this->_upload($apiurl, $params);
		} else {
			$content = $this->_request($apiurl, $params, $method);
		}
		return json_decode($content, true);
	}
	
	public function get($url, $params=array()) {
		return $this->request($url, $params, 'GET');
	}
	
	public function post($url, $params=array()) {
		return $this->request($url, $params, 'POST');
	}
	
	protected function _request($url, $params=array(), $method='GET') {
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
		$this->_setSaeHeader($url);   
		     
		$method = strtoupper($method);
		switch ($method) {
			case 'POST':
				curl_setopt($this->curl, CURLOPT_POST, true);
				break;
			case 'DELETE':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
				break;
			case 'PUT':
				curl_setopt($this->curl, CURLOPT_PUT, true);
				break;
			default:
				break;
		}
		
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($this->curl);
		return $content;
	}
	
	protected function _upload($url, $params=array()) {
		$file = file_get_contents($params['pic']);
		$mime_type = $params['mime_type'];
		$filename = $params['filename'];
		if(empty($mime_type)) {
			$mime_type = 'image/jpg';
		}
		$boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		
		$multipartbody .= $MPboundary . "\r\n";
		$multipartbody .= 'Content-Disposition: form-data; name="pic"; filename="'. $filename .'"'. "\r\n";
		$multipartbody .= "Content-Type: {$mime_type}\r\n\r\n";
		$multipartbody .= $file. "\r\n";

		$multipartbody .= $MPboundary . "\r\n";
		$multipartbody .= 'content-disposition: form-data; name="source"'."\r\n\r\n";
		$multipartbody .= $params['source']."\r\n";
		
		$multipartbody .= $MPboundary . "\r\n";
		$multipartbody .= 'content-disposition: form-data; name="status"'."\r\n\r\n";
		$multipartbody .= $params['status']."\r\n";
		$multipartbody .= "\r\n". $endMPboundary;
		
		curl_setopt($this->curl, CURLOPT_POST, true);	
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $multipartbody);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		$http_header = array("Content-Type: multipart/form-data; boundary=$boundary");
		
		$this->_setSaeHeader($url, $http_header);
		
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($this->curl);
		return $content;
	}
	
	protected function _setSaeHeader($url, $http_header=array()) {
		$header_array = array();
        $header_array["FetchUrl"] = $url;
        $header_array['TimeStamp'] = date('Y-m-d H:i:s');
        $header_array['AccessKey'] = $_SERVER['HTTP_ACCESSKEY'];
        
        $content="FetchUrl";
        $content.=$header_array["FetchUrl"];
        $content.="TimeStamp";
        $content.=$header_array['TimeStamp'];
        $content.="AccessKey";
        $content.=$header_array['AccessKey'];
            
        $header_array['Signature'] = base64_encode(hash_hmac('sha256',$content, $_SERVER['HTTP_SECRETKEY'] ,true));
		
        foreach($header_array as $k => $v)
            array_push($http_header, $k.': '.$v);
            
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $http_header); 
        curl_setopt($this->curl, CURLOPT_URL, SAE_FETCHURL_SERVICE_ADDRESS);
	}
	
	public function __destruct() {
		curl_close($this->curl);
	}
}

?>