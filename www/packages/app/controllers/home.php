<?php

class _home extends \MeshMVC\Controller {
    function sign() {
        return route("/");
    }
    function run() {
        view("html")
            ->from("n00s.html");
    }
}

class _fetcher extends \MeshMVC\Controller {
    function sign() {
        return route("/fetcher");
    }
    function run() {
        echo file_get_contents("https://google.com/complete/search?output=toolbar&q=".urlencode($_GET["z"]));
        die();
    }
}

class _web_status extends \MeshMVC\Controller {
    function sign() {
        return route("/status");
    }
    function run() {
        $url = "https://".urlencode($_GET["url"]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
    }
}

class _full_tech extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/tech");
    }

    static function date_parse($date): string {
        $trimmedDate = preg_replace("/^on\s|\s@\d{2}:\d{2}[AP]M$/i", "", $date);
        $date = DateTime::createFromFormat('l F d, Y', $trimmedDate);
        if ($date) {
            return $date->format('Y-m-d');
        } else {
            return "???";
        }
    }

    function run() {
        $nz = view("html")
            ->from("https://slashdot.org")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($nz);

        $allData = [];

        foreach ($doc->find("h2.story") as $el) {
            $this_data = pq($el);
            $allData[] = [
                "title" => trim($this_data->find("a:eq(0)")->text()),
                "link" => trim($this_data->find("a:eq(0)")->attr("href")),
                "release_date" => self::date_parse($this_data->parents("article")->find("time")->text())
            ];
        }

        echo json_encode($allData, JSON_PRETTY_PRINT);
        die();
    }

}

class _full_music extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/music");
    }

    static function parse_date($str) {
        // October 4, 2024
        $date = DateTime::createFromFormat('F j, Y', $str);
        return $date->format('Y-m-d');
    }

    static function fetchAlbums(): bool|string {
        $url = "https://www.metacritic.com/browse/albums/release-date/new-releases/date?view=condensed";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: n00z/1.0 (contact@luclaverdure.com)'
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    static function fetchLatestAlbums(): array {
        $allAlbums = [];

        $data = self::fetchAlbums();

        if (!$data) {
            return $allAlbums; // Return empty if no data is fetched
        }

        $doc = \phpQuery::newDocumentHTML($data);

        foreach ($doc->find(".clamp-list tr") as $el) {
            $album = pq($el);
            $allAlbums[] = [
                "title" => trim($album->find("h3")->text()),
                "artist" => trim($album->find(".artist")->text()),
                "release_date" => self::parse_date(trim($album->find(".details span")->text()))
            ];
        }

        return $allAlbums;
    }

    function run() {
        $latestAlbums = self::fetchLatestAlbums();
        echo json_encode($latestAlbums, JSON_PRETTY_PRINT);
        die();
    }

}


class _full_movies extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/movies");
    }

    static function fetchMovies($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    function run() {
        $apiKey = '9a684caf9b21f686ab16477873af9692';

        // Upcoming movies
        $upcomingUrl = "https://api.themoviedb.org/3/movie/upcoming?api_key=$apiKey&language=en-US&page=1";
        $upcomingMovies = self::fetchMovies($upcomingUrl);

        // Movies in theaters
        $inTheatersUrl = "https://api.themoviedb.org/3/movie/now_playing?api_key=$apiKey&language=en-US&page=1";
        $inTheatersMovies = self::fetchMovies($inTheatersUrl);

        $allMovies = array_merge($upcomingMovies["results"], $inTheatersMovies["results"]);

        echo json_encode($allMovies, JSON_PRETTY_PRINT);

        die();
    }

}


class _full_games extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/games");
    }

    static function parse_date($date) {
        $date = new DateTime($date);
        $formattedDate = $date->format('Y-m-d');
        return $formattedDate;
    }


    function run() {
        $allData = [];

        $data = view("html")
            ->from("https://internal-prod.apigee.fandom.net/v1/xapi/finder/metacritic/web?sortBy=-releaseDate&productType=games&page=1&releaseYearMin=".date("Y")."&releaseYearMax=".(date("Y")+1)."&lastTouchedInput=releaseYearMax&offset=0&limit=50&apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u")
            ->toString();

        $data2 = view("html")
            ->from("https://www.metacritic.com/browse/game/all/all/all-time/new/?releaseYearMin=".date("Y")."&releaseYearMax=".date("Y")."&releaseType=coming-soon&page=1")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($data2);

        $allData = json_decode($data, true)["data"]["items"];

        foreach ($doc->find(".c-finderProductCard") as $el) {
            $this_data = pq($el);
            $allData[] = [
                "title" => trim($this_data->find("h3 span:eq(0)")->text()),
                "link" => "#",
                "releaseDate" => self::parse_date(trim($this_data->find(".c-finderProductCard_meta span:eq(0)")->text()))
            ];
        }


        echo json_encode($allData, JSON_PRETTY_PRINT);

        die();
    }

}

class _news extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/news");
    }

    static function parse_date($str) {
        $date = new DateTime($str);
        $formattedDate = $date->format('Y-m-d');
        return $formattedDate;
    }

    function run() {
        $html = view("html")
            ->from("https://news.google.com/rss?hl=en-CA&gl=US&ceid=CA:en")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($html);
        $allData = [];
        foreach ($doc->find("item") as $el) {
            $this_data = pq($el);
            $allData[] = [
                "title" => trim($this_data->find("title")->text()),
                "link" => trim($this_data->find("link")->text()),
                "pubDate" => self::parse_date(trim($this_data->find("pubDate")->text()))
            ];
        }
        echo json_encode($allData, JSON_PRETTY_PRINT);
        die();
    }

}