<?php
/**
 * TOP API: taobao.sp.service.site.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-28 17:28:31
 */
class SpServiceSiteGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "taobao.sp.service.site.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
