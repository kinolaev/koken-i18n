<?php

class KokenI18n extends KokenPlugin {

	function __construct()
	{
		$this->require_setup = true;
		$this->register_filter('api.album', 'kokenI18nAlbum');
		$this->register_filter('api.content', 'kokenI18nContent');
		$this->register_filter('api.text', 'kokenI18nText');
		$this->register_filter('site.template', 'kokenI18nTemplate');
		$this->register_filter('site.title', 'kokenI18nTitle');
		$this->register_filter('site.output', 'kokenI18nOutput');
	}

	function kokenI18nAlbum($data)
	{
		$lang = count(explode($this->data->separator, $this->data->lang_string));
		$fields = array('title', 'summary', 'description');
		foreach ($fields as $field) {
			if (count(explode($this->data->separator, $data[$field])) === $lang)
				$data[$field] = '<span class="k-i18n">'.$data[$field].'</span>';
		}
		return $data;
	}

	function kokenI18nContent($data)
	{
		$lang = count(explode($this->data->separator, $this->data->lang_string));
		$fields = array('title', 'caption');
		foreach ($fields as $field) {
			if (count(explode($this->data->separator, $data[$field])) === $lang)
				$data[$field] = '<span class="k-i18n">'.$data[$field].'</span>';
		}
		return $data;
	}

	function kokenI18nText($data)
	{
		$lang = count(explode($this->data->separator, $this->data->lang_string));
		$fields = array('title', 'excerpt', 'content');
		foreach ($fields as $field) {
			if (count(explode($this->data->separator, $data[$field])) === $lang)
				$data[$field] = '<span class="k-i18n">'.$data[$field].'</span>';
		}
		return $data;
	}

	function kokenI18nTemplate($template) {
		$lang = count(explode($this->data->separator, $this->data->lang_string));
		$vars['labels.album.plural'] = explode($this->data->separator, $this->data->l_album_pl);
		$vars['labels.album.singular'] = explode($this->data->separator, $this->data->l_album_sg);
		$vars['labels.archive.plural'] = explode($this->data->separator, $this->data->l_archive_pl);
		$vars['labels.archive.singular'] = explode($this->data->separator, $this->data->l_archive_sg);
		$vars['labels.category.plural'] = explode($this->data->separator, $this->data->l_category_pl);
		$vars['labels.category.singular'] = explode($this->data->separator, $this->data->l_category_sg);
		$vars['labels.content.plural'] = explode($this->data->separator, $this->data->l_content_pl);
		$vars['labels.content.singular'] = explode($this->data->separator, $this->data->l_content_sg);
		$vars['labels.essay.plural'] = explode($this->data->separator, $this->data->l_essay_pl);
		$vars['labels.essay.singular'] = explode($this->data->separator, $this->data->l_essay_sg);
		$vars['labels.favorite.plural'] = explode($this->data->separator, $this->data->l_favorite_pl);
		$vars['labels.favorite.singular'] = explode($this->data->separator, $this->data->l_favorite_sg);
		$vars['labels.page.plural'] = explode($this->data->separator, $this->data->l_page_pl);
		$vars['labels.page.singular'] = explode($this->data->separator, $this->data->l_page_sg);
		$vars['labels.set.plural'] = explode($this->data->separator, $this->data->l_set_pl);
		$vars['labels.set.singular'] = explode($this->data->separator, $this->data->l_set_sg);
		$vars['labels.tag.plural'] = explode($this->data->separator, $this->data->l_tag_pl);
		$vars['labels.tag.singular'] = explode($this->data->separator, $this->data->l_tag_sg);
		$vars['labels.timeline.plural'] = explode($this->data->separator, $this->data->l_timeline_pl);
		$vars['labels.timeline.singular'] = explode($this->data->separator, $this->data->l_timeline_sg);
		foreach ($vars as $key => $value) {
			if (count($value) === $lang)
				$template = preg_replace('/\{\{\s*'.$key.'.*\}\}/', '<span class="k-i18n">'.implode($this->data->separator, $value).'</span>', $template);
		}
		return $template;
	}

	function kokenI18nTitle($title) {
		$lang = count(explode($this->data->separator, $this->data->lang_string));
		$objects['album'] = explode($this->data->separator, $this->data->l_album_pl);
		$objects['archive'] = explode($this->data->separator, $this->data->l_archive_pl);
		$objects['category'] = explode($this->data->separator, $this->data->l_category_pl);
		$objects['content'] = explode($this->data->separator, $this->data->l_content_pl);
		$objects['essay'] = explode($this->data->separator, $this->data->l_essay_pl);
		$objects['favorite'] = explode($this->data->separator, $this->data->l_favorite_pl);
		$objects['page'] = explode($this->data->separator, $this->data->l_page_pl);
		$objects['set'] = explode($this->data->separator, $this->data->l_set_pl);
		$objects['tag'] = explode($this->data->separator, $this->data->l_tag_pl);
		$objects['timeline'] = explode($this->data->separator, $this->data->l_timeline_pl);
		$titles = $objects[(Koken::$source['type'] === 'categories' ? 'category' : rtrim(Koken::$source['type'], 's'))];
		if (count($titles) === $lang)
			$title = '<span class="k-i18n">'.implode($this->data->separator, $titles).'</span>';
		return $title;
	}

	function kokenI18nOutput($html)
	{
		$cookie = isset($_COOKIE['koken_i18n']) ? $_COOKIE['koken_i18n'] : $this->data->lang_default;
		Koken::$cache_path = str_replace('/cache.', '/cache.'.$cookie.'.', Koken::$cache_path);

		$html = preg_replace_callback("/<span class=\"k-i18n\">(.*?)<\/span>/s", function($matches) {
			$lang = explode($this->data->separator, $this->data->lang_string);
			$text = explode($this->data->separator, $matches[1]);
			$cookie = isset($_COOKIE['koken_i18n']) ? $_COOKIE['koken_i18n'] : $this->data->lang_default;
	
			foreach ($lang as $i => $l) {
				$text[$l] = $text[$i];
				unset($text[$i]);
			}

			if (empty($text[$cookie]))
				return $text[$this->data->lang_default];
			else
				return $text[$cookie];
		}, $html);

		return $html;
	}
}
