<?php
/**
 * Created by PhpStorm.
 * User: lhy
 * Date: 2022/10/9
 * Time: 13:47
 *
 * 获取 win / linux 的mac地址
 * https://www.kancloud.cn/idcpj/python/1358799
 */


class MacAddress{

    public $return_array = array(); // 返回带有MAC地址的字串数组
    public $mac_addr;


    static function  getMacAddr(){
        $static = new static();
        $os_type = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'windows' : 'linux';
        switch(strtolower($os_type)){
            case "linux":
                $static->forLinux();
                break;
            case "solaris":
                break;
            case "unix":
                break;
            case "aix":
                break;
            default:
                $static->forWindows();
                break;
        }
        $temp_array = array();
        foreach($static->return_array as $value){
            if(preg_match(
                "/ether ([0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f])/i", $value, $temp_array)){
                $static->mac_addr = $temp_array[1];
                break;
            }
        }
        unset($temp_array);
        $static->mac_addr = str_replace("-", "", $static->mac_addr);
        $static->mac_addr = str_replace(":", "", $static->mac_addr);

        return $static->mac_addr;
    }

    function forWindows(){
        @exec("ipconfig /all", $this->return_array);
        if($this->return_array) return $this->return_array;else{
            $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
            if(is_file($ipconfig)) @exec($ipconfig." /all", $this->return_array);else
                @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);

            return $this->return_array;
        }
    }

    private function forLinux()
    {
        if(is_file('/usr/sbin/ip')){
            $path='/usr/sbin/ip';
        }else if(is_file('/sbin/ip')){
            $path='/sbin/ip';
        }else{
            throw new \RuntimeException("not found  ip ");
        }

        @exec("{$path} addr", $return_array);
        foreach ($return_array as $value) {

            if (
            preg_match(
                "/ether ([0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f])/i",
                $value,
                $temp_array
            )
            ) {
                $this->mac_addr = $temp_array[1];
                break;
            }
        }
    }

}

// 使用
MacAddress::getMacAddr();