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
    /** 設定値のスキーマ */
    protected const OPTIONS_SCHEMA = [
        'schema_id' => [
            'title' => '',
            'type' => 'hidden',
            'default' => null
        ],
        'cache_lifetime' => [
            'title' => 'Cache Lifetime',
            'type' => 'number',
            'default' => 20
        ]
    ];

    /** プラグイン本体クラス */
    protected readonly object   $plugin;

    /** 設定値のキー名 */
    protected readonly string   $option_key;

    /** キーが存在するかどうか */
    protected bool  $is_exists;

    /** 設定値 */
    protected array $values;

    public function __construct(object $plugin)
    {
        $this->plugin = $plugin;
        $this->option_key = $plugin->get_prefix() . 'options';

        // 取得する
        $values = \get_option($this->option_key, false);
        $this->is_exists = ($values !== false);

        // デフォルト値の作成
        $default_values = \array_map(fn(array $schema): mixed => $schema['default'], self::OPTIONS_SCHEMA);
        $this->values = \array_merge($default_values, ($this->is_exists)? $values : array());
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
                \trigger_error("Creating option {$this->option_key} was failed.", E_USER_WARNING);
            }
        }
        else {
            // 保存する
            if (($result = \update_option($this->option_key, $this->values)) !== true) {
                \trigger_error("Updating option {$this->option_key} was failed.", E_USER_WARNING);
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

    public function __get(string $name): mixed
    {
        if (\array_key_exists($name, $this->values)) {
            return $this->values[$name];
        }
        else {
            \trigger_error("Not found {$name} in options.", E_USER_WARNING);
        }
    }

    public function __set(string $name, mixed $value): void
    {
        if (\array_key_exists($name, $this->values)) {
            $this->values[$name] = $value;
        }
        else {
            \trigger_error("Not found {$name} in options.", E_USER_WARNING);
        }
    }

    /**
     * 設定画面用登録処理.
     */
    public function admin_register_settings(array $args = array()): void
    {
        $args = \wp_parse_args($args, [
            'option_group' => $this->plugin->get_name(),
            'section_callback_func' => null,
            'section_callback_args' => array()
        ]);

        \register_setting($args['option_group'], $this->option_key);
        \add_settings_section(
            'wporg_section_developers',
            __('The Matrix has you.'),
            $args['section_callback_func'],
            $args['option_group'],
            $args['section_callback_args']
        );
        foreach (self::OPTIONS_SCHEMA as $key => $schema) {
            \add_settings_field(
                $key,
                __($schema['title']),
                [$this, 'wporg_field_pill_cb'],
                $args['option_group'],
                'wporg_section_developers',
                ($schema + ['id' => $key])
            );
        }
    }

    public function wporg_field_pill_cb(array $args): void
    {
        //
        switch ($args['type'])
        {
            case 'hidden':
                echo "<input id=\"{$args['id']}\" name=\"{$this->option_key}[{$args['id']}]\" type=\"hidden\" value=\"{$this->values[$args['id']]}\" />\n";
                break;
            case 'number':
                echo "<input id=\"{$args['id']}\" name=\"{$this->option_key}[{$args['id']}]\" type=\"number\" value=\"{$this->values[$args['id']]}\" />\n";
                break;
        }
    }

}
