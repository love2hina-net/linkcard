<?php
namespace love2hina\wordpress\linkcard;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/public
 * @author     webmaster@love2hina.net
 */
class LinkcardPublic
{

    /**
     * プラグイン本体クラス.
     *
     * @access	protected
     * @var     Linkcard	$plugin
     */
    protected readonly Linkcard $plugin;

    public function __construct(Linkcard $plugin)
    {
        $this->plugin = $plugin;

        $this->plugin->add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        $this->plugin->add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        \add_shortcode('linkcard', [$this, 'shortcode_callback']);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles(): void
    {
        $this->plugin->enqueue_style('public', 'public/css/linkcard-public.css');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts(): void
    {
        $this->plugin->enqueue_script('public', 'public/js/linkcard-public.js', ['jquery']);
    }

    public function shortcode_callback(array $atts, ?string $content): string
    {
        $args = \shortcode_atts([
            'url' => null,
            'title' => null,
            'excerpt' => null
        ], $atts);

        if (empty($args['url']) || ($args['url'] = \trim($args['url'])) == '') {
            // URLが空
            \trigger_error('Called with invalid url that was empty.', E_USER_WARNING);
            return '';
        }

        // キャッシュから取得
        $data = $this->plugin->database->query_cache($args['url']);
        if ($data === null) {
            // OGP情報を取得
            $this->plugin->load_module('includes/OpenGraph.php');
            $graph = new OpenGraph($args['url']);
            $data = [
                'url' => $args['url'],
                'title' => $graph->title,
                'description' => $graph->description,
                'image' => $graph->image,
                'site' => $graph->site_name
            ];

            // キャッシュに追加
            $this->plugin->database->insert_cache($data);
        }

        // 補完処理
        $fn_comp = fn(?string $value, ?string $default) => ($value === null || \trim($value) == '')? $default : $value;
        $info = [
            'url' => $data['url'],
            'title' => $fn_comp($args['title'], $data['title']),
            'description' => \wp_trim_words($fn_comp($args['excerpt'], $data['description']), 60),
            'image' => $data['image'],
            'site' => $data['site']
        ];
        $info['image_tag'] = (empty($info['image']))? '' : "<img class=\"col-md-4 img-fluid rounded-start\" src=\"{$info['image']}\" alt=\"{$info['title']}\">";
        $info['location'] = ((empty($info['site']))? '' : "{$info['site']} - ") . $info['url'];

        // 外部リンク用ブログカードHTML出力
        return <<<"EOF"
            <div class="linkcard card mb-3">
                <a href="{$info['url']}" target="_blank" class="row g-0">
                    {$info['image_tag']}
                    <div class="col-md-8 card-body">
                        <h5 class="card-title">{$info['title']}</h5>
                        <p class="card-text">{$info['description']}</p>
                        <p class="card-text"><small class="text-muted">{$info['location']}</small></p>
                    </div>
                </a>
            </div>
            EOF;
    }

}
