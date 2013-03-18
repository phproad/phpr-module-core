<?php

class Core_Rss
{
	private $title;
	private $alt_url;
	private $description;
	private $entries;
	private $rss_link;

	public function __construct( $title, $alt_url, $description, $rss_link )
	{
		$this->title = $title;
		$this->alt_url = $alt_url;
		$this->description = $description;
		$this->entries = array();
		$this->rss_link = $rss_link;
	}

	// Adds an entry to the channel
	public function add_entry($title, $link, $id, $update_date, $summary, $create_date, $author, $body)
	{
		$entry = array();

		$entry['title'] = $title;
		$entry['link'] = $link;
		$entry['update_date'] = $update_date;
		$entry['id'] = $id;
		$entry['summary'] = $summary;
		$entry['create_date'] = $create_date;
		$entry['author'] = $author;
		$entry['body'] = $body;

		$this->entries[] = (object)$entry;
	}

	// Returns XML string representing the channel
	public function to_xml()
	{
		$result = null;
		$gmt_format = '%a, %d %b %Y %H:%M:%S GMT';
		$gmt_now = Phpr_DateTime::gmt_now()->format($gmt_format);

		$result .= '<?xml version="1.0" encoding="UTF-8"?>';
		$result .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>'."\n";
		$result .= '<atom:link href="'.$this->rss_link.'" rel="self" type="application/rss+xml" />'."\n";
		$result .= '<title>'.self::cdata_wrap($this->title).'</title>'."\n";
		$result .= '<link>'.$this->alt_url."</link>\n";
		$result .= '<description>'.self::cdata_wrap($this->description).'</description>'."\n";
		$result .= '<pubDate>'.$gmt_now.'</pubDate>'."\n";
		$result .= '<lastBuildDate>'.$gmt_now.'</lastBuildDate>'."\n";
		$result .= '<generator>PHPR</generator>'."\n";
		
		foreach ($this->entries as $entry)
		{
			$result .= '<item>'."\n";
			$result .= '<title>'.self::cdata_wrap($entry->title).'</title>'."\n";
			$result .= '<link>'.$entry->link.'</link>'."\n";
			$result .= '<guid>'.$entry->link.'</guid>'."\n";
			$result .= '<pubDate>'.$entry->create_date->format($gmt_format).'</pubDate>'."\n";
			$result .= '<description>'.self::cdata_wrap($entry->body).'</description>'."\n";
			// $result .= '<content type="text/html" xml:lang="en-US"><![CDATA['.$entry->body.']]></content>'."\n";
			$result .= '</item>'."\n";
		}

		$result .= '</channel></rss>';
		return $result;
	}
	
	public static function cdata_wrap($value)
	{
		$value = str_replace(']]>', ']]&gt;', $value);
		return '<![CDATA['.$value.']]>';
	}
}
