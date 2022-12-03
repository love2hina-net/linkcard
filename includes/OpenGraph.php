<?php
namespace love2hina\wordpress\linkcard;

/*
  Copyright 2010 Scott MacVicar

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

    Original can be found at https://github.com/scottmac/opengraph/blob/master/OpenGraph.php

*/

class _HtmlLoader
{

    /**
     * ドキュメント.
     *
     * @access  private
     * @var     DOMDocument $document
     */
    private readonly \DOMDocument   $document;

    /**
     * 読み込み時に使用した文字コード.
     *
     * @access  public
     * @var     string  $loading_charset
     */
    public readonly string  $loading_charset;

    /**
     * 検出した文字コード.
     *
     * @access  public
     * @var     string  $detected_charset
     */
    public string           $detected_charset = '';

    public function __construct(string $html, string $charset = 'utf-8')
    {
        $this->document = new \DOMDocument();
        $this->loading_charset = $charset;

        @$this->document->loadHTML("<?xml encoding=\"$charset\"?>" . $html);
    }

    public function parse(): array | bool
    {
        $values = array();
        $nonOgDescription = null;

        foreach ($this->document->getElementsByTagName('meta') as $tag)
        {
            if ($tag->hasAttribute('charset')) {
                $this->detected_charset = $tag->getAttribute('charset');

                if (\strcasecmp($this->detected_charset, $this->loading_charset) != 0) {
                    // 読み込みに使用した文字コードと検出した文字コードが異なる
                    if (\strcasecmp($this->detected_charset, $this->document->encoding) == 0) {
                        // DOMDocumentによって、リカバリーされた
                        \trigger_error("DOMDocument was swiched encoding to {$this->document->encoding} automatically.", E_USER_WARNING);
                    }
                    else {
                        \trigger_error("It is needed reloading a document, because a document was read {$this->loading_charset}, but actually {$this->detected_charset}.", E_USER_WARNING);
                        return true;
                    }
                }
            }

            if ($tag->hasAttribute('property') &&
                \strpos($tag->getAttribute('property'), 'og:') === 0)
            {
                $key = \strtr(\substr($tag->getAttribute('property'), 3), '-', '_');
                $values[$key] = $tag->getAttribute('content');
            }

            // Added this if loop to retrieve description values from sites like the New York Times who have malformed it.
            if ($tag->hasAttribute('value') && $tag->hasAttribute('property') &&
                \strpos($tag->getAttribute('property'), 'og:') === 0)
            {
                $key = \strtr(\substr($tag->getAttribute('property'), 3), '-', '_');
                $values[$key] = $tag->getAttribute('value');
            }
            // Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
            if ($tag->hasAttribute('name') && $tag->getAttribute('name') === 'description') {
                $nonOgDescription = $tag->getAttribute('content');
            }
        }

        // Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
        if (!isset($values['title'])) {
            $titles = $this->document->getElementsByTagName('title');
            if ($titles->length > 0) {
                $values['title'] = $titles->item(0)->textContent;
            }
        }
        if (!isset($values['description']) && $nonOgDescription) {
            $values['description'] = $nonOgDescription;
        }

        // Fallback to use image_src if ogp::image isn't set.
        if (!isset($values['image'])) {
            $domxpath = new DOMXPath($this->document);
            $elements = $domxpath->query("//link[@rel='image_src']");

            if ($elements->length > 0) {
                $domattr = $elements->item(0)->attributes->getNamedItem('href');
                if ($domattr) {
                    $values['image'] = $domattr->value;
                    $values['image_src'] = $domattr->value;
                }
            }
        }

        return $values;
    }
}

class OpenGraph implements \Iterator
{
    /**
     * There are base schema's based on type, this is just
     * a map so that the schema can be obtained
     */
    public static $TYPES = array(
        'activity' => array('activity', 'sport'),
        'business' => array('bar', 'company', 'cafe', 'hotel', 'restaurant'),
        'group' => array('cause', 'sports_league', 'sports_team'),
        'organization' => array('band', 'government', 'non_profit', 'school', 'university'),
        'person' => array('actor', 'athlete', 'author', 'director', 'musician', 'politician', 'public_figure'),
        'place' => array('city', 'country', 'landmark', 'state_province'),
        'product' => array('album', 'book', 'drink', 'food', 'game', 'movie', 'product', 'song', 'tv_show'),
        'website' => array('blog', 'website'),
    );

    /**
     * Holds all the Open Graph values we've parsed from a page
     */
    private readonly array  $_values;

    /**
     * Fetches a URL and parses it for Open Graph data.
     *
     * @param string    $url    URI to page to parse for Open Graph data
     */
    public function __construct(string $url)
    {
        $response = null;
        $curl = \curl_init($url);

        try {
            \curl_setopt($curl, CURLOPT_FAILONERROR, true);
            \curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            \curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            \curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            \curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

            $response = \curl_exec($curl);
        }
        finally {
            \curl_close($curl);
        }

        if (!empty($response)) {
            $charset = 'utf-8';
            $result = null;

            do {
                $loader = new _HtmlLoader($response, $charset);
                $result = $loader->parse();
                if ($result === true) {
                    $charset = $loader->detected_charset;
                }
            } while ($result === true);

            if (\is_array($result)) {
                $this->_values = $result;
            }
            else {
                throw new \Exception("Couldn't parse from {$url}.");
            }
        }
        else {
            throw new \Exception("Couldn't get from {$url}.");
        }
    }

    /**
     * Helper method to access attributes directly
     * Example:
     * $graph->title
     *
     * @param string    $key    Key to fetch from the lookup
     */
    public function __get(string $key): mixed
    {
        if (\array_key_exists($key, $this->_values)) {
            return $this->_values[$key];
        }

        if ($key === 'schema') {
            foreach (self::$TYPES as $schema => $types) {
                if (\array_search($this->_values['type'], $types)) {
                    return $schema;
                }
            }
        }

        return null;
    }

    /**
     * Return all the keys found on the page
     *
     * @return array
     */
    public function keys(): array
    {
        return \array_keys($this->_values);
    }

    /**
     * Helper method to check an attribute exists
     *
     * @param string    $key
     */
    public function __isset(string $key): bool
    {
        return \array_key_exists($key, $this->_values);
    }

    /**
     * Will return true if the page has location data embedded
     *
     * @return boolean Check if the page has location data
     */
    public function hasLocation(): bool
    {
        if (\array_key_exists('latitude', $this->_values) && \array_key_exists('longitude', $this->_values)) {
            return true;
        }

        $address_keys = array('street_address', 'locality', 'region', 'postal_code', 'country_name');
        $valid_address = true;
        foreach ($address_keys as $key) {
            $valid_address = ($valid_address && \array_key_exists($key, $this->_values));
        }
        return $valid_address;
    }

    /**
     * Iterator code
     */
    private $_position = 0;
    public function current(): mixed { return \current($this->_values); }
    public function key(): mixed { return \key($this->_values); }
    public function next(): void { \next($this->_values); ++$this->_position; }
    public function rewind(): void { \reset($this->_values); $this->_position = 0; }
    public function valid(): bool { return $this->_position < \sizeof($this->_values); }
}
