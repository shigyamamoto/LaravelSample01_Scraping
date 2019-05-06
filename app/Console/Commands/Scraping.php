<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;

class Scraping extends Command
{

    protected $signature = 'scraping:some';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = "https://github.com/shigyamamoto/LaravelSample01_Scraping";
        $goutte = GoutteFacade::request('GET', $url);

        // Aタグのhref要素を抽出してみる
        $goutte->filter('a')->each(function ($a) use ($url) {
            $href = $a->attr("href");
            if ($this->checker_href($href, $url)) {
                echo $a->attr("href") . "\n";
            }
        });
    }

    public function checker_href($str, $url)
    {
        // 要素がemptyの場合にはfalse
        if (empty($str)) {
            return false;
        }
        // 要素がページ内リンクの場合にはfalse(#から始まる文字列)
        if (preg_match('/^#/', $str)) {
            return false;
        }
        // 要素がgithub内リンクの場合にはfalse(http,httpsから始まらない文字列)
        if (! preg_match('/^http(s)*:\/\//', $str)) {
            return false;
        }
        // 要素がmailtoの場合にはfalse
        if (preg_match('/^mailto/', $str)) {
            return false;
        }

        // 要素がgithub内リンクの場合にはfalse(hostが自身と一致する文字列)
        $url_parsed = parse_url($url);
        $str_parsed = parse_url($str);
        if (! isset($url_parsed['host']) || ! isset($str_parsed['host'])) {
            return false;
        }
        if ($url_parsed['host'] == $str_parsed['host']) {
            return false;
        }
        return true;
    }
}
