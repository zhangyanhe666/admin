<?php
/**
 * TOP API: taobao.crm.member.group.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-28 17:28:31
 */
class CrmMemberGroupGetRequest
{
	/** 
	 * 会员Nick
	 **/
	private $buyerNick;
	
	private $apiParas = array();
	
	public function setBuyerNick($buyerNick)
	{
		$this->buyerNick = $buyerNick;
		$this->apiParas["buyer_nick"] = $buyerNick;
	}

	public function getBuyerNick()
	{
		return $this->buyerNick;
	}

	public function getApiMethodName()
	{
		return "taobao.crm.member.group.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerNick,"buyerNick");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
