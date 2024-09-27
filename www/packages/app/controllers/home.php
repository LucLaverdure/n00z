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

    function run() {
        $nz = view("html")
            ->from("https://slashdot.org")
            ->filter("h2.story span")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($nz);

        $doc->find("span")->remove();
        $doc->find("a[title='']")->remove();
        $doc->find("a")->removeAttr("onclick");

        echo '<div>'.$doc.'</div>';
        die();
    }

}

class _full_music extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/music");
    }

    function run() {
        $nz = view("html")
            ->from("https://pitchfork.com/reviews/albums/")
            ->filter("div.summary-item__content")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($nz);

        $doc->find("a span")->remove();
        $doc->find(".summary-item__byline-date-icon")->remove();

        echo $doc;
        die();
    }

}



class _full_movies extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/movies");
    }

    function run() {
        $nz = view("html")
            ->from("https://www.imdb.com/calendar")
            ->filter("article ul .ipc-metadata-list-summary-item__tc")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($nz);

        $doc->find("ul")->remove();

        echo $doc;
        die();
    }

}


class _full_games extends \MeshMVC\Controller {

    function sign() {
        return route("/n00z/games");
    }

    function run() {
        $nz = view("html")
            ->from("https://store.steampowered.com/explore/new/")
            ->filter(".tab_content_items")
            ->toString();

        $doc = \phpQuery::newDocumentHTML($nz);

        $doc->find("div.tab_item_cap,div.tab_item_discount,div.tab_item_details")->remove();

        echo $doc;
        die();
    }

}