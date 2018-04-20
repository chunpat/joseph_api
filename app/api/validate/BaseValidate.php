<?php
namespace app\api\validate;
use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate{

    public function goCheck($scene = '')
    {
        // 获取http传入的参数
        // 对这些参数做检验
        $request = Request::instance();
        $params = $request->param();

        $result = $this->batch()
            ->check($params,[],$scene);
        if (!$result)
        {
            $e = new ParameterException([
                    'msg' => $this->error,
                ]);
            throw $e;
        }
        else
        {
            return true;
        }
    }


    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0)
        {
            return true;
        }
        else
        {
            return false;
            //            return $field.'必须是正整数';
        }
    }

    protected function isMobile($value){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $rst = preg_match($rule,$value);
        if($rst){
            return true;
        }
        return false;
    }


    protected function isNotEmpty($value,$rule='',$date='',$field='')
    {
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

    function getDataByRule($varArr)
    {
        if(array_key_exists('uid',$varArr) || array_key_exists('user_id',$varArr)){
            throw new Exception('存在含有关键的的uid或者user_id的恶意篡改行为请求！');
        }
        $RstArr = [];
        foreach($this->rule as $k =>$v){
            $RstArr[$k] = $varArr[$k];
        }
        return $RstArr;

    }

    /**
     * 校验日期格式是否正确
     *
     * @param string $date 日期
     * @param string $formats 需要检验的格式数组
     * @return boolean
     */
    function checkDateIsValid($date) {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }

        $formats = ["Y-m-d","d/m/Y"];
        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }

        return false;
    }



}
