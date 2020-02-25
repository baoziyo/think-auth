<?php

namespace think\auth\helper;

class Data
{
    /**
     * 返回多层栏目
     * @param $datas 操作的数组
     * @param int $pid 一级PID的值
     * @param string $html 栏目名称前缀
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @param int $level 不需要传参数（执行时调用）
     * @return array
     */
    static public function channelLevel($datas, $pid = 0, $html = '&nbsp;', $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        if (empty($datas)) {

            return array();
        }

        $arr = array();
        foreach ($datas as $data) {
            if ($v[$fieldPid] == $pid) {
                $arr[$data[$fieldPri]] = $v;
                $arr[$data[$fieldPri]]['_level'] = $level;
                $arr[$data[$fieldPri]]['_html'] = str_repeat($html, $level - 1);
                $arr[$data[$fieldPri]]['_data'] = self::channelLevel($datas, $data[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1);
            }
        }

        return $arr;
    }

    /**
     * 获得所有子栏目
     * @param $datas 栏目数据
     * @param int $pid 操作的栏目
     * @param string $html 栏目名前字符
     * @param string $fieldPri 表主键
     * @param string $fieldPid 父id
     * @param int $level 等级
     * @return array
     */
    static public function channelList($datas, $pid = 0, $html = '&nbsp;', $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        $datas = self::_channelList($datas, $pid, $html, $fieldPri, $fieldPid, $level);

        if (empty($datas)) {

            return $datas;
        }

        foreach ($datas as $key => $data) {
            if ($data['_level'] == 1) {
                continue;
            }

            $datas[$key]['_first'] = false;
            $datas[$key]['_end'] = false;
            if (!isset($datas[$key - 1]) || $datas[$key - 1]['_level'] != $data['_level']) {
                $datas[$key]['_first'] = true;
            }
            if (isset($datas[$key + 1]) && $datas[$key]['_level'] > $datas[$key + 1]['_level']) {
                $datas[$key]['_end'] = true;
            }
        }

        $category = array();
        foreach ($datas as $data) {
            $category[$data[$fieldPri]] = $data;
        }
        return $category;
    }

    //只供channelList方法使用
    static private function _channelList($datas, $pid = 0, $html = '&nbsp;', $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        if (empty($datas)) {

            return array();
        }

        $arr = array();
        foreach ($datas as $data) {
            $id = $data[$fieldPri];
            if ($data[$fieldPid] == $pid) {
                $data['_level'] = $level;
                $data['_html'] = str_repeat($html, $level - 1);
                array_push($arr, $data);
                $tmp = self::_channelList($datas, $id, $html, $fieldPri, $fieldPid, $level + 1);
                $arr = array_merge($arr, $tmp);
            }
        }

        return $arr;
    }

    /**
     * 获得树状数据
     * @param $datas 数据
     * @param $title 字段名
     * @param string $fieldPri 主键id
     * @param string $fieldPid 父id
     * @return array
     */
    static public function tree($datas, $title, $fieldPri = 'id', $fieldPid = 'pid')
    {
        if (!is_array($datas) || empty($datas)) {

            return array();
        }

        $arrays = Data::channelList($datas, 0, '', $fieldPri, $fieldPid);
        foreach ($arrays as $key => $array) {
            $str = '';
            if ($array['_level'] > 2) {
                for ($i = 1; $i < $array['_level'] - 1; $i++) {
                    $str .= '   │';
                }
            }

            if ($array['_level'] != 1) {
                $t = $title ? $array[$title] : '';
                if (isset($arrays[$key + 1]) && $arrays[$key + 1]['_level'] >= $arrays[$key]['_level']) {
                    $arrays[$key]['_name'] = $str . '    ├─ ' . $array['_html'] . $t;
                } else {
                    $arrays[$key]['_name'] = $str . '    └─ ' . $array['_html'] . $t;
                }
            } else {
                $arrays[$key]['_name'] = $array[$title];
            }
        }

        //设置主键为$fieldPri
        $datas = array();
        foreach ($arrays as $array) {
            $datas[$array[$fieldPri]] = $array;
        }

        return $datas;
    }

    /**
     * 获得所有父级栏目
     * @param $datas 栏目数据
     * @param $sid 子栏目
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @return array
     */
    static public function parentChannel($datas, $sid, $fieldPri = 'id', $fieldPid = 'pid')
    {
        if (empty($datas)) {

            return $datas;
        }

        $arr = array();
        foreach ($datas as $data) {
            if ($data[$fieldPri] == $sid) {
                $arr[] = $data;
                $_n = self::parentChannel($datas, $data[$fieldPid], $fieldPri, $fieldPid);
                if (!empty($_n)) {
                    $arr = array_merge($arr, $_n);
                }
            }
        }
        return $arr;
    }

    /**
     * 判断$s_cid是否是$d_cid的子栏目
     * @param $datas 栏目数据
     * @param $sid 子栏目id
     * @param $pid 父栏目id
     * @param string $fieldPri 主键
     * @param string $fieldPid 父id字段
     * @return bool
     */
    static function isChild($datas, $sid, $pid, $fieldPri = 'id', $fieldPid = 'pid')
    {
        $_data = self::channelList($datas, $pid, '', $fieldPri, $fieldPid);
        foreach ($_data as $c) {
            //目标栏目为源栏目的子栏目
            if ($c[$fieldPri] == $sid) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检测是不否有子栏目
     * @param $datas 栏目数据
     * @param $cid 要判断的栏目cid
     * @param string $fieldPid 父id表字段名
     * @return bool
     */
    static function hasChild($datas, $cid, $fieldPid = 'pid')
    {
        foreach ($datas as $data) {
            if ($data[$fieldPid] == $cid) {

                return true;
            }
        }

        return false;
    }

    /**
     * 递归实现迪卡尔乘积
     * @param $arr 操作的数组
     * @param array $tmp
     * @return array
     */
    static function descarte($arr, $tmp = array())
    {
        static $n_arr = array();
        foreach (array_shift($arr) as $v) {
            $tmp[] = $v;
            if ($arr) {
                self::descarte($arr, $tmp);
            } else {
                $n_arr[] = $tmp;
            }
            array_pop($tmp);
        }
        return $n_arr;
    }
}
