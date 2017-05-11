<?php
/**
 * TOP API: taobao.waimai.address.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-28 17:28:31
 */
class WaimaiAddressGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "taobao.waimai.address.get";
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
