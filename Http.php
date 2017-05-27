<?php

class Http
{
    const SEND_REAL_IP = 'send_real_ip';

    /**
     * Online http/https
     *
     */
    private $_schema;

    /**
     * Online 主机
     *
     */
    private $_host;
    
    /**
     * Online 端口
     *
     */
    private $_port;
    
    /**
     * Online 路径
     *
     */
    private $_path;
    
    /**
     * SSL 证书
     *
     */
    private $_cert;
    
    /**
     * SSL 证书通行证
     *
     */
    private $_certPasswd;
    
    /**
     * SSL 密钥
     *
     */
    private $_key;
    
    /**
     * 超时
     * @var int
     */
    private $_timeout;
    
    /**
     * 是否发送用户IP到后端
     * @var bool
     */
    private $_sendRealIp = true;

    // ------------------------------------------------------------------------    

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        
    }

    // ------------------------------------------------------------------------    

    /**
     * Get Schema
     *
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    // ------------------------------------------------------------------------

    /**
     * Set Schema
     *
     */
    public function setSchema($schema)
    {
        $this->_schema = $schema;

        return $this;
    }

    /**
     * Get Host
     *
     */
    public function getHost()
    {
        return $this->_host;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set Host
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->_host = $host;
        
        return $this;
    }

    // ------------------------------------------------------------------------    

    /**
     * Get Port
     *
     */
    public function getPort()
    {
        return $this->_port;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set Port
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->_port = $port;
        
        return $this;
    }

    // ------------------------------------------------------------------------    

    /**
     * Get Path
     *
     */
    public function getPath()
    {
        return $this->_path;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set Path
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->_path = $path;
        
        return $this;
    }

    // ------------------------------------------------------------------------    

    /**
     * Get SSL 证书
     *
     */
    public function getCert()
    {
        return $this->_cert;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set SSL 证书
     * @param $cert
     * @return $this
     */
    public function setCert($cert)
    {
        $this->_cert = $cert;
        
        return $this;
    }

    // ------------------------------------------------------------------------        

    /**
     * Get SSL 证书通行证
     *
     */
    public function getCertPasswd()
    {
        return $this->_certPasswd;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set SSL 证书通行证
     * @param $certPasswd
     * @return $this
     */
    public function setCertPasswd($certPasswd)
    {
        $this->_certPasswd = $certPasswd;
        
        return $this;
    }

    // ------------------------------------------------------------------------   

    /**
     * Get SSL 密钥
     *
     */
    public function getKey()
    {
        return $this->_key;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set SSL 密钥
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->_key = $key;
        
        return $this;
    }

    // ------------------------------------------------------------------------   

    /**
     * Get Timeout
     *
     * @access public
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    // ------------------------------------------------------------------------    

    /**
     * Set Timeout
     *
     * @param integer
     * @return $this
     * @access public
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isSendRealIp()
    {
        return $this->_sendRealIp;
    }
    
    /**
     * @param boolean $sendUserIp
     * @return $this
     */
    public function setSendRealIp($sendUserIp)
    {
        $this->_sendRealIp = $sendUserIp ? true : false;
    
        return $this;
    }

    // ------------------------------------------------------------------------    

    /**
     * Online Query
     *
     * @param string $queryStr
     * @param string $method
     * @param null $postFields
     * @param null|array|string $cookieFields
     * @return array
     * @access public
     */
    public function query($queryStr = '', $method = 'GET', $postFields = null, $cookieFields = null)
    {
        $options = array();
        
        $schema = 'http';
        
        if (isset($this->_cert, $this->_certPasswd, $this->_key)) {
            
            $schema = 'https';
            
            $options[CURLOPT_SSLCERT] = $this->_cert;
            $options[CURLOPT_SSLCERTPASSWD] = $this->_certPasswd;
            $options[CURLOPT_SSLKEY] = $this->_key;
        }
        if (isset($this->_schema)) {
            $schema = $this->_schema;
        }

        $url = "$schema://{$this->_host}";
        
        if (is_numeric($this->_port)) {
            
            $url .= ":{$this->_port}";
        }
        
        $url .= $this->_path;
        
        if (!empty($queryStr)) {
            
            $url .= "?$queryStr";
        }
        
        if (is_numeric($this->_timeout)) {
            
            $options[CURLOPT_TIMEOUT] = (int)$this->_timeout;
        }
        
        $method = strtoupper($method);
        
        if ($method === 'POST' && !empty($postFields)) {
            
            $options[CURLOPT_POSTFIELDS] = $postFields;
            
        } else if ($method === 'PUT') {
            
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            
            if (!empty($postFields)) {
                
                $options[CURLOPT_POSTFIELDS] = $postFields;
            }
            
            $options[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postFields)
            );
        }

        if (isset($cookieFields)) {
            if (is_array($cookieFields)) {
                $cookieStrs = array();
                foreach ($cookieFields as $key => $value) {
                    $cookieStrs[] = urlencode($key) . "=" . urlencode($value);
                }
                $cookieStr = implode('; ', $cookieStrs);
            } else {
                $cookieStr = $cookieFields;
            }
            if ($cookieStr != '') {
                $options[CURLOPT_COOKIE] = $cookieStr;
            }
        }

        $res = self::sole($url, $options, array(
            self::SEND_REAL_IP => $this->_sendRealIp ? true : false,
        ));

        return $res;
    }
    
    public static function sole($url, $options = array(), $extra = array(self::SEND_REAL_IP => true))
    {
        $realIp = Infra_Function::getClientIP();
        
        $default = array(
            CURLOPT_SSLVERSION => 3,
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HTTPHEADER => array(
                'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
                'Cache-Control: no-cache',
            ),
        );
    
        if (isset($extra[self::SEND_REAL_IP]) && $extra[self::SEND_REAL_IP]) {
            $default[CURLOPT_HTTPHEADER][] = 'X-Forwarded-For: ' . $realIp;
            $default[CURLOPT_HTTPHEADER][] = 'X-Real-IP: ' . $realIp;
        }
        
        $options = $options + $default;
        
        $options[CURLOPT_URL] = $url;
        
        $ch = curl_init();

        curl_setopt_array($ch, $options);
        
        $res['txt'] = curl_exec($ch);
        
        $res['inf'] = curl_getinfo($ch);

        $code = curl_errno($ch);

        if ($code > 0) {
            $res['err'] = $code;
            $res['error'] = curl_error($ch);
            
        } elseif (isset($res['inf']['http_code'])
            && $res['inf']['http_code'] >= 400
            && $res['inf']['http_code'] < 600
        ) {
        
        }

        if (isset($options[CURLOPT_POSTFIELDS])) {
            $res['inf']['postFields'] = $options[CURLOPT_POSTFIELDS];
        }
        
        curl_close($ch);
        
        // ...DEBUG... 需要重新设计

        $debugSigns = explode(',', $_GET['debug']);
        if (((in_array(3, $debugSigns) && strpos($options[CURLOPT_URL], '/solr/queans/data') !== false)
                || in_array('api', $debugSigns))
            && Infra_Function::isInnerNetwork()
        ) {
            $outputArr = array(
                'url' => $options[CURLOPT_URL],
                'postfields' => $options[CURLOPT_POSTFIELDS],
            );
            if (isset($options[CURLOPT_COOKIE])) {
                $outputArr['cookie'] = $options[CURLOPT_COOKIE];
            }
            $outputArr['error'] = $res['error'];
            $outputArr['time'] = $res['inf']['total_time'];
            $outputArr['txt'] = $res['txt'];
            header('Content-Type: text/html; charset=utf-8');
            highlight_string("<?php\n" . preg_replace(array('|=> \n\s*|', '|\n((?: {2})*) ?([^ ])|'), array("=> ", "\n\$1\$1\$2"), var_export($outputArr, true))); //die;
        }
        
        return $res;
    }

    public function multiQuery($queryStrs, $options = array(), $postStrs = array())
    {
        $urlBase = "http://{$this->_host}:{$this->_port}{$this->_path}";

        $options = array_merge(array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => $this->_timeout,
        ), $options);

        $urls = array();

        foreach ($queryStrs as $key => $queryStr) {
            $joinChar = strpos($queryStr, '?') === false ? '?' : '&';
            $urls[$key] = $urlBase . $joinChar . $queryStr;
        }

        return self::multi($urls, $options, $postStrs);
    }

    public static function multi($urls, $options = array(), $postStrs = array())
    {
        $queue = curl_multi_init();
        
        $map = array();
        
        foreach ($urls as $key => $url) {
            
            $ch = curl_init();
            
            $default = array(
                CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)',
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_NOSIGNAL => true,
                CURLOPT_TIMEOUT => 7
            );
            
            $curOptions = $options + $default;

            $curOptions[CURLOPT_URL] = $url;

            if (isset($postStrs[$key]) && $postStrs[$key] != '') {
                $curOptions[CURLOPT_POST] = 1;
                $curOptions[CURLOPT_POSTFIELDS] = $postStrs[$key];
            }

            curl_setopt_array($ch, $curOptions);

            curl_multi_add_handle($queue, $ch);
            
            $map[$key] = array($ch, $url);
        }
        
        $active = null;
        
        do {
            
            $mrc = curl_multi_exec($queue, $active);
            
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        
        while ($active > 0 && $mrc == CURLM_OK) {
            
            if (curl_multi_select($queue, 0.5) != -1) {
                
                do {
                    
                    $mrc = curl_multi_exec($queue, $active);
                    
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        
        $responses = array();
        
        foreach ($map as $key => $item) {
            
            $ch = $item[0];

            $res = array(
                'url' => $item[1],
                'inf' => curl_getinfo($ch),
                'err' => curl_error($ch),
                'txt' => curl_multi_getcontent($ch)
            );
            $responses[$key] = $res;

            $debugSigns = explode(',', $_GET['debug']);
            if (in_array(3, $debugSigns) || in_array('api', $debugSigns)) {
                $outputArr = array(
                    'url' => $res['url'],
                    //'inf' => curl_getinfo($ch),
                    //'err' => curl_error($ch),
                    //'txt' => $res['txt'],
                );
                if (isset($postStrs[$key]) && !empty($postStrs[$key])) {
                    $outputArr['postfields'] = $postStrs[$key];
                }
                $outputArr['err'] = curl_error($ch);
                $outputArr['time'] = $res['inf']['total_time'];
                $outputArr['txt'] = $res['txt'];
                highlight_string("<?php\n" . preg_replace(array('|=> \n\s*|', '|\n((?: {2})*)?([^ ])|'), array("=> ", "\n\$1\$1\$2"), var_export($outputArr, true)));
            }
            
            curl_multi_remove_handle($queue, $ch);
            
            curl_close($ch);
        }
        
        curl_multi_close($queue);
        
        return $responses;
    }
}
/* curl error code */
/*
[1] => 'CURLE_UNSUPPORTED_PROTOCOL',
[2] => 'CURLE_FAILED_INIT',
[3] => 'CURLE_URL_MALFORMAT',
[4] => 'CURLE_URL_MALFORMAT_USER',
[5] => 'CURLE_COULDNT_RESOLVE_PROXY',
[6] => 'CURLE_COULDNT_RESOLVE_HOST',
[7] => 'CURLE_COULDNT_CONNECT',
[8] => 'CURLE_FTP_WEIRD_SERVER_REPLY',
[9] => 'CURLE_REMOTE_ACCESS_DENIED',
[11] => 'CURLE_FTP_WEIRD_PASS_REPLY',
[13] => 'CURLE_FTP_WEIRD_PASV_REPLY',
[14] =>'CURLE_FTP_WEIRD_227_FORMAT',
[15] => 'CURLE_FTP_CANT_GET_HOST',
[17] => 'CURLE_FTP_COULDNT_SET_TYPE',
[18] => 'CURLE_PARTIAL_FILE',
[19] => 'CURLE_FTP_COULDNT_RETR_FILE',
[21] => 'CURLE_QUOTE_ERROR',
[22] => 'CURLE_HTTP_RETURNED_ERROR',
[23] => 'CURLE_WRITE_ERROR',
[25] => 'CURLE_UPLOAD_FAILED',
[26] => 'CURLE_READ_ERROR',
[27] => 'CURLE_OUT_OF_MEMORY',
[28] => 'CURLE_OPERATION_TIMEDOUT',
[30] => 'CURLE_FTP_PORT_FAILED',
[31] => 'CURLE_FTP_COULDNT_USE_REST',
[33] => 'CURLE_RANGE_ERROR',
[34] => 'CURLE_HTTP_POST_ERROR',
[35] => 'CURLE_SSL_CONNECT_ERROR',
[36] => 'CURLE_BAD_DOWNLOAD_RESUME',
[37] => 'CURLE_FILE_COULDNT_READ_FILE',
[38] => 'CURLE_LDAP_CANNOT_BIND',
[39] => 'CURLE_LDAP_SEARCH_FAILED',
[41] => 'CURLE_FUNCTION_NOT_FOUND',
[42] => 'CURLE_ABORTED_BY_CALLBACK',
[43] => 'CURLE_BAD_FUNCTION_ARGUMENT',
[45] => 'CURLE_INTERFACE_FAILED',
[47] => 'CURLE_TOO_MANY_REDIRECTS',
[48] => 'CURLE_UNKNOWN_TELNET_OPTION',
[49] => 'CURLE_TELNET_OPTION_SYNTAX',
[51] => 'CURLE_PEER_FAILED_VERIFICATION',
[52] => 'CURLE_GOT_NOTHING',
[53] => 'CURLE_SSL_ENGINE_NOTFOUND',
[54] => 'CURLE_SSL_ENGINE_SETFAILED',
[55] => 'CURLE_SEND_ERROR',
[56] => 'CURLE_RECV_ERROR',
[58] => 'CURLE_SSL_CERTPROBLEM',
[59] => 'CURLE_SSL_CIPHER',
[60] => 'CURLE_SSL_CACERT',
[61] => 'CURLE_BAD_CONTENT_ENCODING',
[62] => 'CURLE_LDAP_INVALID_URL',
[63] => 'CURLE_FILESIZE_EXCEEDED',
[64] => 'CURLE_USE_SSL_FAILED',
[65] => 'CURLE_SEND_FAIL_REWIND',
[66] => 'CURLE_SSL_ENGINE_INITFAILED',
[67] => 'CURLE_LOGIN_DENIED',
[68] => 'CURLE_TFTP_NOTFOUND',
[69] => 'CURLE_TFTP_PERM',
[70] => 'CURLE_REMOTE_DISK_FULL',
[71] => 'CURLE_TFTP_ILLEGAL',
[72] => 'CURLE_TFTP_UNKNOWNID',
[73] => 'CURLE_REMOTE_FILE_EXISTS',
[74] => 'CURLE_TFTP_NOSUCHUSER',
[75] => 'CURLE_CONV_FAILED',
[76] => 'CURLE_CONV_REQD',
[77] => 'CURLE_SSL_CACERT_BADFILE',
[78] => 'CURLE_REMOTE_FILE_NOT_FOUND',
[79] => 'CURLE_SSH',
[80] => 'CURLE_SSL_SHUTDOWN_FAILED',
[81] => 'CURLE_AGAIN',
[82] => 'CURLE_SSL_CRL_BADFILE',
[83] => 'CURLE_SSL_ISSUER_ERROR',
[84] => 'CURLE_FTP_PRET_FAILED',
[84] => 'CURLE_FTP_PRET_FAILED',
[85] => 'CURLE_RTSP_CSEQ_ERROR',
[86] => 'CURLE_RTSP_SESSION_ERROR',
[87] => 'CURLE_FTP_BAD_FILE_LIST',
[88] => 'CURLE_CHUNK_FAILED'
*/
