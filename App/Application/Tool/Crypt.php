<?php

/**
 * Created by PhpStorm.
 * User: mrgeneral
 * Date: 16/4/1
 * Time: 下午3:15
 */
namespace Application\Tool;
class Crypt
{

    private $data;
    private $key;
    private $module;
    private $complexTypes = false;
    const HMAC_ALGORITHM = 'sha1';
    const DELIMITER = '!';
    const MCRYPT_MODULE = 'des';
    const MCRYPT_MOD = 'cfb';
    const MINIMUM_KEY_LENGTH = 8;
    public  $wechatAppid = 'wx90fa7c0f2b5257c0';
    public  $wechatSecret = '97e4834563716a63e10fc96d25752fff';

    public  $encryptKey = 'lYC22PRxlc4Of5D%%k%p';
    public  $encryptKeyExpireTime = 31104000;
    function __construct()
    {
        $this->checkEnvironment();
        $this->setModule(mcrypt_module_open(self::MCRYPT_MODULE, '', self::MCRYPT_MOD, ''));
    }

    /**
     * @throws Exception
     * 检查环境
     */
    private function checkEnvironment()
    {
        if ((!extension_loaded('mcrypt')) || (!function_exists('mcrypt_module_open'))) {
            throw new Exception('The PHP mcrypt extension must be installed for encryption', 1);
        }
        if (!in_array(self::MCRYPT_MODULE, mcrypt_list_algorithms())) {
            throw new Exception("The cipher used self::MCRYPT_MODULE does not appear to be supported by the installed version of libmcrypt", 1);
        }
    }

    /**
     * @param $data
     * 设置加密内容
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param $key
     * @return bool
     * 设置加密key
     */
    public function setKey($key)
    {
        if (strlen($key) < self::MINIMUM_KEY_LENGTH)
            return false;
        $this->key = $key;
        return true;
    }

    /**
     * @param $module
     * 设置加密方式
     */
    private function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @param $complexTypes
     * 非字符串类型
     */
    public function setComplexTypes($complexTypes)
    {
        $this->complexTypes = $complexTypes;
    }

    private function getData()
    {
        return $this->data;
    }

    private function getKey()
    {
        return $this->key;
    }

    private function getModule()
    {
        return $this->module;
    }

    private function getComplexTypes()
    {
        return $this->complexTypes;
    }

    public function encryptUserId($data)
    {
        $data .= self::DELIMITER . time();
        $this->setKey($this->encryptKey);
        $this->setData($data);
        return base64_encode($this->encrypt());
    }

    public function decryptUserId($data)
    {
        $this->setKey($this->encryptKey);
        $this->setData(base64_decode($data));
        $result = $this->decrypt();
        if ($result) {
            $result = explode(self::DELIMITER, $result);
            if (isset($result[1]{9}) && time() - $result[1] < $this->encryptKeyExpireTime)
                return $result[0];
        }
        return false;
    }

    /**
     * @return string
     * 加密
     * 注释了强校验
     */
    public function encrypt()
    {
        mt_srand();
        $init_vector = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->getModule()), MCRYPT_RAND);
        $key = substr(sha1($this->getKey()), 0, mcrypt_enc_get_key_size($this->getModule()));
        mcrypt_generic_init($this->getModule(), $key, $init_vector);
        if ($this->getComplexTypes()) {
            $this->setData(serialize($this->getData()));
        }
        $cipher = mcrypt_generic($this->getModule(), $this->getData());
//        $hmac = hash_hmac(self::HMAC_ALGORITHM, $init_vector . self::DELIMITER . $cipher, $this->getKey());
        $encoded_init_vector = base64_encode($init_vector);
        $encoded_cipher = base64_encode($cipher);
        return $encoded_init_vector . self::DELIMITER . $encoded_cipher;
    }

    /**
     * @return mixed|string
     * @throws Exception
     * 解密
     * 注释了强校验
     */
    public function decrypt()
    {
        $elements = explode(self::DELIMITER, $this->getData());
        if (count($elements) != 2)
            return false;
        $init_vector = base64_decode($elements[0]);
        $cipher = base64_decode($elements[1]);
        /* $given_hmac = $elements[2];
         $hmac = hash_hmac(self::HMAC_ALGORITHM, $init_vector . self::DELIMITER . $cipher, $this->getKey());
         if ($given_hmac != $hmac)
             return false;*/
        $key = substr(sha1($this->getKey()), 0, mcrypt_enc_get_key_size($this->getModule()));
        mcrypt_generic_init($this->getModule(), $key, $init_vector);
        $result = mdecrypt_generic($this->getModule(), $cipher);
        if ($this->getComplexTypes()) {
            return unserialize($result);
        }
        return $result;
    }

    public function __destruct()
    {
        @mcrypt_generic_deinit($this->getModule());
        mcrypt_module_close($this->getModule());
    }

}

?>