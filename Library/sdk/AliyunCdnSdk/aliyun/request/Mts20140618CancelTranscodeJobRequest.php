<?php
/**
 * TOP API: mts.aliyuncs.com.CancelTranscodeJob.2014-06-18 request
 * 
 * @author auto create
 * @since 1.0, 2014-11-28 17:28:31
 */
class Mts20140618CancelTranscodeJobRequest
{
	/** 
	 * 媒资ID
	 **/
	private $mediaId;
	
	/** 
	 * 转码模板ID
	 **/
	private $templateId;
	
	/**
	 * 仅用于渠道商发起API调用时，指定访问的资源拥有者的ID
	 **/
	private $ownerId;
	
	/**
	 *仅用于渠道商发起API调用时，指定访问的资源拥有者的账号
	 **/
    private  $ownerAccount;
    
    /**
     *API调用者试图通过API调用来访问别人拥有但已经授权给他的资源时，
     *通过使用该参数来声明此次操作涉及到的资源是谁名下的,该参数仅官网用户可用
     **/
    private $resourceOwnerAccount;
    
	private $apiParas = array();
	
	public function setMediaId($mediaId)
	{
		$this->mediaId = $mediaId;
		$this->apiParas["MediaId"] = $mediaId;
	}

	public function getMediaId()
	{
		return $this->mediaId;
	}

	public function setTemplateId($templateId)
	{
		$this->templateId = $templateId;
		$this->apiParas["TemplateId"] = $templateId;
	}

	public function getTemplateId()
	{
		return $this->templateId;
	}

	
	public function setOwnerId($ownerId)
	{
		$this->ownerId = $ownerId;
		$this->apiParas["OwnerId"] = $ownerId;
	}
	
	public function getOwnerId()
	{
		return $this->ownerId;
	}
	
	public function setOwnerAccount($ownerAccount)
	{
		$this->ownerAccount = $ownerAccount;
		$this->apiParas["OwnerAccount"] = $ownerAccount;
	}
	
	public function getOwnerAccount()
	{
		return $this->ownerAccount;
	}
	
	public function setResourceOwnerAccount($resourceOwnerAccount)
	{
		$this->resourceOwnerAccount = $resourceOwnerAccount;
		$this->apiParas["ResourceOwnerAccount"] = $resourceOwnerAccount;
	}
	
	public function getResourceOwnerAccount()
	{
		return $this->resourceOwnerAccount;
	}

	public function getApiMethodName()
	{
		return "mts.aliyuncs.com.CancelTranscodeJob.2014-06-18";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->mediaId,"mediaId");
		RequestCheckUtil::checkNotNull($this->templateId,"templateId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}