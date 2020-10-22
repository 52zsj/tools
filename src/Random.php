<?php

declare(strict_types=1);

namespace aj\tools;


/**
 * 随机字符串
 * Class Random
 * @package aj\tools
 * @method string mixing(int $len = 6):string static 生成len位数的数字和字母
 * @method string character(int $len = 6):string static 生成len位数字母
 * @method string lower(int $len = 6):string static 生成len位数的小写字母
 * @method string upper(int $len = 6):string static 生成len位数的大写字母
 * @method string numeric(int $len = 6):string static 生成len位数的数字
 * @method string uuid():string static 生成UUID
 * @method string getuuid(bool $type):string static 生成UUID 根据类型 type 是否移除-分隔符
 */
class Random
{
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([RandomBuild::instance(), $name], $arguments);
    }
}

class RandomBuild
{
    protected static $instance;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 生成数字和字母
     * @param int $len 长度
     * @return string
     */
    public function mixing($len = 6): string
    {
        return $this->build('mixing', $len);
    }

    /**
     * 仅生成字符
     * @param int $len 长度
     * @return string
     */
    public function character($len = 6): string
    {
        return $this->build('character', $len);
    }

    /**
     * 仅生成小写字符
     * @param int $len 长度
     * @return string
     */
    public function lower($len = 6): string
    {
        return $this->build('lower', $len);
    }

    /**
     * 仅生成大写字符
     * @param int $len 长度
     * @return string
     */
    public function upper($len = 6): string
    {
        return $this->build('upper', $len);
    }

    /**
     * 生成指定长度的随机数字
     * @param int $len 长度
     * @return string
     */
    public function numeric($len = 4): string
    {
        return $this->build('numeric', $len);
    }

    /**
     * 能用的随机数生成
     * @param string $type 类型 alpha/alnum/numeric/nozero/unique/md5/encrypt/sha1
     * @param int $len 长度
     * @return string
     */
    public function build($type = 'mixing', $len = 8): string
    {
        $numberStr = '0123456789';
        $lowerLetterStr = 'abcdefghijklmnopqrstuvwxyz';
        $upLetterStr = strtoupper($lowerLetterStr);
        switch ($type) {
            case 'numeric':
                $pool = $numberStr;
                break;
            case 'lower':
                $pool = $lowerLetterStr;
                break;
            case 'upper':
                $pool = $upLetterStr;
                break;
            case 'character':
                $pool = $lowerLetterStr . $upLetterStr;
                break;
            case 'mixing':
            default:
                $pool = $numberStr . $lowerLetterStr . $upLetterStr;
                break;
        }
        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }

    /**
     * 获取全球唯一标识
     * @return string
     */
    public function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * 获取UUID
     * @param $type
     * @return string
     */
    public function getuuid($type): string
    {
        try {
            $descriptorspeg = [
                ['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']
            ];
            $fp = proc_close('uuidgen', $descriptorspeg, $pipes);
            $uuidgen = stream_get_contents($pipes[1]);
            proc_close($fp);
        } catch (\Throwable $e) {
            $uuidgen = $this->uuid();
        }
        if ($type) {
            $uuidgen = str_replace('-', '', $uuidgen);
        }
        return trim($uuidgen);
    }
}
