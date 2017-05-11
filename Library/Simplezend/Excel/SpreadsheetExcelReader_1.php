<?php



namespace Library\Excel;

class SpreadsheetExcelReader{
    const BIFF8             =   0x600;
    const BIFF7             =   0x500;
    const WORKBOOKGLOBALS   =   0x5;
    const WORKSHEET         =   0x10;
    const TYPE_BOF          =   0x809;
    const TYPE_EOF          =   0x0a;
    const TYPE_BOUNDSHEET   =   0x85;
    const TYPE_DIMENSION    =   0x200;
    const TYPE_ROW          =   0x208;
    const TYPE_DBCELL       =   0xd7;
    const TYPE_FILEPASS     =   0x2f;
    const TYPE_NOTE         =   0x1c;
    const TYPE_TXO          =   0x1b6;
    const TYPE_RK           =   0x7e;
    const TYPE_RK2          =   0x27e;
    const TYPE_MULRK        =   0xbd;
    const TYPE_MULBLANK     =   0xbe;
    const TYPE_INDEX        =   0x20b;
    const TYPE_SST          =   0xfc;
    const TYPE_EXTSST       =   0xff;
    const TYPE_CONTTNUE     =   0x3c;
    const TYPE_LABEL        =   0x204;
    const TYPE_LABELSST     =   0xfd;
    const TYPE_NUMBER       =   0x203;
    const TYPE_NAME         =   0x18;
    const TYPE_ARRAY        =   0x221;
    const TYPE_STRING       =   0x207;
    const TYPE_FORMULA      =   0x406;
    const TYPE_FORMULA2     =   0x6;
    const TYPE_FORMAT       =   0x41e;
    const TYPE_XF           =   0xe0;
    const TYPE_BOOLERR      =   0x205;
    const TYPE_UNKNOWN      =   0xffff;
    const TYPE_NINETEENFOUR =   0x22;
    const TYPE_MERGEDCELLS  =   0xE5;
    const UTCOFFSETDAYS     =   25569;
    const UTCOFFSETDAYS1904 =   24107;
    const MSINADAY          =   86400;
    const DEF_NUM_FORMAT    =   "%s";
    
    public $boundsheets     =   array();
    public $formatRecords   =   array();
    public $sst             =   array();
    
    public $sheets          =   array();
    public $data;
    public $_ole;
    public $_defaultEncoding=   'UTF-8';
    public $_defaultFormat  =   self::DEF_NUM_FORMAT;
    public $_columnsFormat  =   array();
    public $_encoderFunction=   'iconv';
    public $_rowoffset      =   1;
    public $_coloffset      =   1;
    public $dateFormats     =   array(
        0xe => "d/m/Y",
        0xf => "d-M-Y",
        0x10 => "d-M",
        0x11 => "M-Y",
        0x12 => "h:i a",
        0x13 => "h:i:s a",
        0x14 => "H:i",
        0x15 => "H:i:s",
        0x16 => "d/m/Y H:i",
        0x2d => "i:s",
        0x2e => "H:i:s",
        0x2f => "i:s.S"
    );
    public $numberFormats   =   array(
        0x1 => "%1.0f", // "0"
        0x2 => "%1.2f", // "0.00",
        0x3 => "%1.0f", //"#,##0",
        0x4 => "%1.2f", //"#,##0.00",
        0x5 => "%1.0f", /*"$#,##0;($#,##0)",*/
        0x6 => '$%1.0f', /*"$#,##0;($#,##0)",*/
        0x7 => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x8 => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x9 => '%1.0f%%', // "0%"
        0xa => '%1.2f%%', // "0.00%"
        0xb => '%1.2f', // 0.00E00",
        0x25 => '%1.0f', // "#,##0;(#,##0)",
        0x26 => '%1.0f', //"#,##0;(#,##0)",
        0x27 => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x28 => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x29 => '%1.0f', //"#,##0;(#,##0)",
        0x2a => '$%1.0f', //"$#,##0;($#,##0)",
        0x2b => '%1.2f', //"#,##0.00;(#,##0.00)",
        0x2c => '$%1.2f', //"$#,##0.00;($#,##0.00)",
        0x30 => '%1.0f'//"##0.0E0";
     ); 
    public $pos =   0;
    
    public function __construct() {
        $this->_ole =   new OLERead();
    }
    
    public function setOutputEncoding($encoding){
        $this->_defaultEncoding =   $encoding;
        return $this;
    }
    
    public function setUTFEncoder($encoder){
        if(in_array($encoder, array('iconv','mb_convert_encoding'))){
            $this->_encoderFunction =   $encoder;
        }
    }
    
    public function setRowColOffset($iOffset){
        $this->_rowoffset   =   $iOffset;
        $this->_coloffset   =   $iOffset;
    }
    
    public function setDefaultFormat($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function setColumnFormat($column, $sFormat){
        $this->_columnsFormat[$column] = $sFormat;
    }
    public function readDataBit($pos=0){
        if(!empty($pos)){
            $bit    =   substr($this->data, $this->pos,$pos);
            $this->pos  +=  $pos;
        }else{
            $bit    =   $this->data[$this->pos];
            $this->pos  ++;
        }
        return $bit;
    }
    public function movePos($pos){
        $this->pos  +=  $pos;
    }
    public function read($sFileName){
        $res    =   $this->_ole->read($sFileName);
        if($res === false && $this->_ole->error == 1){
            die("The filename {$sFileName} is not readable");
        }
        $this->data     =   $this->_ole->getWorkBook();
        $code           =   $this->decode($this->readDataBit(), $this->readDataBit());
        $length         =   $this->decode($this->readDataBit(), $this->readDataBit());
        $version        =   $this->decode($this->readDataBit(), $this->readDataBit());
        $substreamType  =   $this->decode($this->readDataBit(), $this->readDataBit());
        if(!in_array($version, [self::BIFF8,self::BIFF7])){
            return false;
        }
        if($substreamType != self::WORKBOOKGLOBALS){
            return false;
        }
        $this->movePos($length-4);
        $code           =   $this->decode($this->readDataBit(), $this->readDataBit());
        $length         =   $this->decode($this->readDataBit(), $this->readDataBit());
        while ($code != self::TYPE_EOF){
            $olength    =   $length;
            switch ($code){
                case self::TYPE_SST:
                    $uniqueStrings  =   $this->_GetInt4d();
                    $this->movePos(8);
                    for($i = 0; $i < $uniqueStrings; $i++){
                        if($length == 8){
                            $code    =   $this->decode($this->readDataBit(), $this->readDataBit());
                            $length  +=   $this->decode($this->readDataBit(), $this->readDataBit());
                            if ($opcode != self::TYPE_CONTTNUE) {
                                return -1;
                            }
                        }                        
                        $numChars       = $this->decode($this->readDataBit(), $this->readDataBit());
                        $optionFlags    = ord($this->readDataBit());
                        $asciiEncoding  = (($optionFlags & 0x01) == 0) ;
                        $extendedString = ( ($optionFlags & 0x04) != 0);
                        $richString     = ( ($optionFlags & 0x08) != 0);
                        if ($richString) {
                            $formattingRuns = ord($this->readDataBit()) | (ord($this->readDataBit()) << 8);
                        }
                        if ($extendedString) {
                            $extendedRunLength = $this->_GetInt4d();
                        }
                        $len = ($asciiEncoding)? $numChars : $numChars*2;
                        if ($this->pos + $len < $length) {
                            $retstr  =   $this->readDataBit($len);
                        }else{
                            $len    =   $length - $this->pos;                            
                            $retstr  =   $this->readDataBit($len);
                            $charsLeft = $numChars - (($asciiEncoding) ? $length : ($length / 2));
                            $this->movePos($length);
                            while($charsLeft > 0){
                                $code    =   $this->decode($this->readDataBit(), $this->readDataBit());
                                $length  +=   $this->decode($this->readDataBit(), $this->readDataBit());
                                if ($opcode != self::TYPE_CONTTNUE) {
                                    return -1;
                                }
                                $option = ord($this->readDataBit());
                                if ($asciiEncoding && ($option == 0)) {
                                    $len = min($charsLeft, $length - $this->pos);
                                    $retstr     .=  $this->readDataBit($len);
                                    $charsLeft  -=  $len;
                                    $asciiEncoding = true;
                                }elseif(!$asciiEncoding && ($option != 0)){
                                    $len = min($charsLeft * 2, $length - $this->pos);
                                    $charsLeft -= $len/2;
                                    $asciiEncoding = false;
                                    $retstr .= $this->readDataBit($len);
                                }elseif (!$asciiEncoding && ($option == 0)) {
                                    $len = min($charsLeft, $length - $this->pos);
                                    for ($j = 0; $j < $len; $j++) {
                                        $retstr .= $this->readDataBit().chr(0);
                                    }
                                    $charsLeft      -=  $len;
                                    $asciiEncoding  =   false;
                                }else{
                                    $newstr     =   '';
                                    for ($j = 0; $j < strlen($retstr); $j++) {
                                        $newstr = $retstr[$j].chr(0);
                                    }
                                    $retstr     =   $newstr;
                                    $len = min($charsLeft * 2, $length - $this->pos);
                                    $retstr .= $this->readDataBit($len);
                                    $charsLeft  -=  $len/2;
                                    $asciiEncoding  =   false;
                                }
                                
                            }
                        }
                        $retstr = ($asciiEncoding) ? $retstr : $this->_encodeUTF16($retstr);
                        // echo "Str $i = $retstr\n";
                        if ($richString){
                            $this->movePos(4 * $formattingRuns);
                        }
                        
                        if ($extendedString) {
                            $this->movePos($extendedRunLength);
                        }
                        
                        $this->sst[]    =   $retstr;
                    }
                    break;
                case self::TYPE_FILEPASS:
                    return false;
                    break;
                case self::TYPE_NAME:
                    break;
                case self::TYPE_FORMAT:
                    $this->movePos(4);
                    $indexCode    =   $this->decode($this->readDataBit(), $this->readDataBit());
                    if($version == self::BIFF8){
                        $numchars    =   $this->decode($this->readDataBit(), $this->readDataBit());
                        if(ord($this->readDataBit()) == 0){
                            $formatString   =     $this->readDataBit($numchars);
                        }  else {
                            $formatString   =     $this->readDataBit($numchars*2);
                        }
                    }  else {
                        $numchars       =   ord($this->readDataBit());
                        $formatString   =   $this->readDataBit($numchars*2);
                    }
                    $this->formatRecords[$indexCode] = $formatString;
                    break;
                case self::TYPE_XF:
                    $this->movePos(6);
                    $indexCode    =   $this->decode($this->readDataBit(), $this->readDataBit());
                    if (array_key_exists($indexCode, $this->dateFormats)) {
                        $this->formatRecords['xfrecords'][] = array(
                            'type' => 'date',
                            'format' => $this->dateFormats[$indexCode]
                        );
                    }elseif (array_key_exists($indexCode, $this->numberFormats)) {
                        $this->formatRecords['xfrecords'][] = array(
                            'type' => 'number',
                            'format' => $this->numberFormats[$indexCode]
                        );
                    }else{
                        $isdate = FALSE;
                        if ($indexCode > 0){
                            if (isset($this->formatRecords[$indexCode]))
                                $formatstr = $this->formatRecords[$indexCode];
                            if ($formatstr && preg_match("/[^hmsday\/\-:\s]/i", $formatstr) == 0) { 
                                $isdate = TRUE;
                                $formatstr = str_replace('mm', 'i', $formatstr);
                                $formatstr = str_replace('h', 'H', $formatstr);
                            }
                       }
                       if ($isdate){
                            $this->formatRecords['xfrecords'][] = array(
                            'type' => 'date',
                            'format' => $formatstr,
                            );
                       }else{
                            $this->formatRecords['xfrecords'][] = array(
                            'type' => 'other',
                            'format' => '',
                            'code' => $indexCode
                            );
                        }
                    }
                    break;
                case self::TYPE_NINETEENFOUR:
                    $this->movePos(4);
                    $this->nineteenFour = (ord($this->readDataBit()) == 1);
                    break;
                case self::TYPE_BOUNDSHEET:
                    $this->movePos(4);
                    $rec_offset = $this->_GetInt4d();
                    $rec_typeFlag = ord($this->readDataBit());
                    $rec_visibilityFlag = ord($this->readDataBit());
                    $rec_length = ord($this->readDataBit());
                    if ($version == self::BIFF8){
                        $chartype = ord($this->readDataBit());
                        if ($chartype == 0){
                            $rec_name = $this->readDataBit($rec_length);
                        } else {
                            $rec_name = $this->_encodeUTF16($this->readDataBit($rec_length*2));
                        }
                    }elseif ($version == self::BIFF7){
                       $rec_name = $this->readDataBit($rec_length);
                    }
                    $this->boundsheets[] = array(
                        'name'=>$rec_name,
                        'offset'=>$rec_offset
                    );
                    break;
            }
            $this->pos  =   $olength + 4;
            $code   = $this->decode($this->readDataBit(), $this->readDataBit());
            $length = $this->decode($this->readDataBit(), $this->readDataBit());
        }
        foreach ($this->boundsheets as $key=>$val){
            $this->sn = $key;
            $this->_parsesheet($val['offset']);
        }
        return true;
    }
    
    
    public function decode($d1,$d2){
        return ord($d1) | ord($d2) << 8;
    }
    
    public function _parsesheet($spos){
        $this->_defaultFormat   =   $sFormat;
    }
    public function isDate($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function createDate($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function createNumber($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function addcell($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function _GetIEEE754($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function _encodeUTF16($sFormat){
        $this->_defaultFormat   =   $sFormat;
    }
    public function _GetInt4d(){
        
        $value  =   ord($this->readDataBit()) | (ord($this->readDataBit()) << 8) | (ord($this->readDataBit()) << 16) | (ord($this->readDataBit()) << 24);
        if($value >= 4294967294){
           $value   =   -2; 
        }
        return $value;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}