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
class LinkcardConfig
{
    /** 設定値のキー名 */
    private readonly string $option_key;

    /** キーが存在するかどうか */
    private bool    $is_exists;

    /** 設定値 */
    private array   $values;

    public function __construct(object $plugin)
    {
        $this->option_key = $plugin->get_prefix() . 'options';

        // 取得する
        $values = \get_option($this->option_key, false);
        $this->is_exists = ($values !== false);
        $this->values = ($this->is_exists)? $values : array();
    }

    public function get_option_key(): string
    {
        return $this->option_key;
    }

    public function apply(): bool
    {
        $result = false;

        if (!$this->is_exists) {
            // 存在しない場合、作成する
            if (($result = \add_option($this->option_key, $this->values)) === true) {
                $this->is_exists = true;
            }
            else {
                \trigger_error("creating option {$this->option_key} was failed.", E_USER_WARNING);
            }
        }
        else {
            // 保存する
            if (($result = \update_option($this->option_key, $this->values)) !== true) {
                \trigger_error("updating option {$this->option_key} was failed.", E_USER_WARNING);
            }
        }

        return $result;
    }

    public function keys(): array
    {
        return \array_keys($this->values);
    }

    public function __isset(string $name): bool
    {
        return \array_key_exists($name, $this->values);
    }

    public function __unset(string $name): void
    {
        unset($this->values[$name]);
    }

    public function __get(string $name): mixed
    {
        return (\array_key_exists($name, $this->values))? $this->values[$name] : null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

}
