<?php
namespace love2hina\wordpress\linkcard;

/**
 * 設定値ヘルパークラス.
 *
 * @since      1.0.0
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class Linkcard_Config
{
    /** 設定値のキー名 */
    const OPTION_KEY = LINKCARD_PREFIX . 'options';

    /** 定数展開用 */
    private readonly functon $_echo;

    /** キーが存在するかどうか */
    private bool $is_exists;

    /** 設定値 */
    private array $values;

    public function __construct()
    {
        // 取得する
        $values = \get_option(self::OPTION_KEY, false);
        $this->is_exists = ($values !== false);
        $this->values = ($this->is_exists)? $values : array();

        $this->_echo = fn(string $value): string => $value;
    }

    public function apply(): bool
    {
        $result = false;

        if (!$this->is_exists) {
            // 存在しない場合、作成する
            if (($result = \add_option(self::OPTION_KEY, $this->values)) === true) {
                $this->is_exists = true;
            }
            else {
                trigger_error("creating option {$this->_echo(self::OPTION_KEY)} was failed.", E_USER_WARNING);
            }
        }
        else {
            // 保存する
            if (($result = \update_option(self::OPTION_KEY, $this->values)) !== true) {
                trigger_error("updating option {$this->_echo(self::OPTION_KEY)} was failed.", E_USER_WARNING);
            }
        }

        return $result;
    }

}

?>