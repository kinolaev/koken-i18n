<?php

class KokenI18n extends KokenPlugin {

	function __construct()
	{
		$this->require_setup = true;
		$this->register_filter('api.album', 'kokenI18nAlbum');
		$this->register_filter('api.content', 'kokenI18nContent');
		$this->register_filter('api.text', 'kokenI18nText');
		$this->register_filter('api.truncate', 'kokenI18nTruncate');
		$this->register_filter('site.template', 'kokenI18nLabels');
		$this->register_filter('site.output', 'kokenI18nOutput');
		$this->register_hook('before_closing_body', 'kokenI18nSwitcher');
		$this->register_hook('before_closing_head', 'kokenI18nSwitcherStyle');
	}

	private function kokenI18nWrapper($fields, $data)
	{
		$lang_count = count(explode($this->data->separator, $this->data->lang_string));
		foreach ($fields as $field) {
			if (count(explode($this->data->separator, $data[$field])) === $lang_count)
				if (!preg_match('/^\[\[.*?\]\]$/s', $data[$field]))
					$data[$field] = '[['.$data[$field].']]';
		}
		return $data;
	}

	private function kokenI18nCookie($setcookie = false)
	{
		$cookie = isset($_COOKIE['koken_i18n']) ? $_COOKIE['koken_i18n'] : $this->data->lang_default;

		if($setcookie)
			setcookie('koken_i18n', $cookie, 0, '/');

		return $cookie;
	}

	private function kokenI18nCallback($matches)
	{
		$lang = explode($this->data->separator, $this->data->lang_string);
		$text = explode($this->data->separator, $matches[1]);
		
		if (count($lang) === count($text)) {
			$text = array_combine($lang, $text);
			$cookie = $this->kokenI18nCookie();

			$text = empty($text[$cookie]) ? $text[$this->data->lang_default] : $text[$cookie];

			$text = preg_replace('/^\s*(<\/.+?>\s*|<br.*?>\s*)*|(\s*<[^\/]+?>)*\s*$/', '', $text);

			return $text;
		}

		return $matches[0];
	}

	private function kokenI18nExpand($html)
	{
		return preg_replace_callback('/\[\[((?:.(?!\[\[))+?)\]\]/s', array($this, 'kokenI18nCallback'), $html);
	}

	function kokenI18nAlbum($data)
	{
		return $this->kokenI18nWrapper(array('title', 'summary', 'description'), $data);
	}

	function kokenI18nContent($data)
	{
		return $this->kokenI18nWrapper(array('title', 'caption'), $data);
	}

	function kokenI18nText($data)
	{
		return $this->kokenI18nWrapper(array('title', 'excerpt', 'content'), $data);
	}

	function kokenI18nLabels($tmpl)
	{
		$nums = array('singular', 'plural');
		foreach (Koken::$site['url_data'] as $key => $value) {
			if (is_array($value)) {

				foreach ($nums as $num)
					if (isset($this->data->{'labels_'.$key.'_'.$num}))
						Koken::$site['url_data'][$key][$num] = '[['.$this->data->{'labels_'.$key.'_'.$num}.']]';

			} else if (is_string($value)) {

				if (isset($this->data->{'labels_'.$key}))
					Koken::$site['url_data'][$key] = '[['.$this->data->{'labels_'.$key}.']]';

			}
		}
		return $tmpl;
	}

	function kokenI18nTruncate($str) {
		return $this->kokenI18nExpand($str);
	}

	function kokenI18nOutput($html)
	{
		if ($_SERVER['SCRIPT_NAME'] !== "/preview.php")
			Koken::$cache_path = str_replace('/cache.', '/cache.'.$this->kokenI18nCookie(true).'.', Koken::$cache_path);

		return $this->kokenI18nExpand($html);
	}

	function kokenI18nSwitcher()
	{
		if($this->data->default_switcher) {
			$lang = explode($this->data->separator, $this->data->lang_string);
			$li = '';
			foreach($lang as $l) {
				$li .= '<li><a href="#" data-lang="'.$l.'">'.$l.'</a></li>';
			}
			echo <<<OUT
<ul id="lang-switcher">{$li}</ul>
<script>
$('#lang-switcher a[data-lang="'+$.cookie('koken_i18n')+'"]').parent().addClass('current');
$('#lang-switcher a').on('click', function(){
	if($.cookie('koken_i18n') != $(this).data('lang')) {
		$.cookie('koken_i18n', $(this).data('lang'), { path: '/' });
		location.reload(true);
	}
	return false;
});
</script>
OUT;
		}
	}

	function kokenI18nSwitcherStyle()
	{
		if($this->data->default_switcher_style) {
			echo <<<OUT
<style>
#lang-switcher {
	position:fixed;
	top:4px;
	right:4px;
	z-index:9999;
}
#lang-switcher li {
	display:inline-block;
	margin:4px 0;
	border-right:1px solid;
}
#lang-switcher li:last-child {
	border-right:none;
}
#lang-switcher li.current {
	font-weight:bold;
}
#lang-switcher li a {
	display:block;
	padding:0 4px;
	outline-style:none;
}
</style>
OUT;
		}
	}
}
