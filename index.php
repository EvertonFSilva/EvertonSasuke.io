<!DOCTYPE html>

<?php

ini_set("max_execution_time", "300");

function get_page($url)
{
    $headers = [
        "x-requested-with: XMLHttpRequest",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36",

    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch)["http_code"];
    curl_close($ch);
    return $code == 200 ? $data : $code;
}


function category_names($categories)
{
    $result = [];
    foreach ($categories as $category) {
        array_push($result, $category["name"]);
    }
    return join(", ", $result);
}


function load_manga_list($page, $category, $min_chaps, $max_chaps)
{
    $mangas = json_decode(get_page("https://mangalivre.net/categories/series_list.json?page={$page}&id_category={$category}"), true)["series"];
    if ($mangas) {
        $mangasLen = count($mangas);
        if ($mangasLen > 0) {
            foreach ($mangas as $manga) {
                $name = $manga["name"];
                $chapters = intval($manga["chapters"]);
                $description = $manga["description"];
                $cover = $manga["cover"];
                $score = $manga["score"];
                $link = "https://mangalivre.net" . $manga["link"];
                $categories = category_names($manga["categories"]);
                $is_complete = $manga["is_complete"];

                if ($chapters >= $min_chaps && $chapters <= $max_chaps) {
                    echo '<li class="manga-box"><a href="' . $link . '"><img class="manga-cover" src="' . $cover . '"><div class="manga-info"><span class="manga-title">' . $name . '</span><span class="manga-categories">Gêneros: ' . $categories . '</span><span class="manga-score">Avaliação: ' . $score . '</span><span class="manga-chapters">Número de Capítulos: ' . $chapters . '</span><span class="manga-status">Estado: ' . ($is_complete == 1 ? "Completo" : "Em Lançamento") . '</span><span class="manga-synopsis">Sinopse: ' . $description . '</span></div></a></li>';
                }
            }
            load_manga_list($page + 1, $category, $min_chaps, $max_chaps);
        }
    }
}


//<li class="manga-box">
//  <a href="#">
//    <img class="manga-cover" src="https://static3.mangalivre.net/cdnwp3/capas/Ge-krXaXVJtasHaL7fJ1eA/5965/capa.jpg">
//  <div class="manga-info">
//    <span class="manga-title">teste</span>
//  <span class="manga-categories">Gêneros: Aventura, Romance, Ação</span>
//  <span class="manga-chapters">Número de Capítulos: 50</span>
//  <span class="manga-score">Avaliação: 8</span>
//  <span class="manga-status">Estado: Em Lançamento</span>
//  <span class="manga-synopsis">Sinopse: Em um mundo alternativo onde o Japão manteve seus antigos costumes, Matsuyuri Ayame é
//      uma aprendiz de gueixa que vive junto com sua mãe, irmã e outras aprendizes. Um dia
//      enquanto voltava de uma festividade, Ayame salva um homem misterioso de um cachorro
//      que estava o atacando. Esse homem sai apressado e deixa para trás um console que
//      Ayame acaba encontrando. Curiosa, ela inicia o suposto jogo e se torna uma jogadora,
//      mas ela logo descobrirá que esse "jogo" pode custar sua vida.</span>
//   </div>
// </a>
//</li>

?>

<html>

<head>
    <title>Manga List - MangaLivre</title>
    <style>
        body {
            margin: 0px;
            background-color: #eeeeee;
        }

        a {
            text-decoration: none;
        }

        div,
        span {
            display: block;
        }

        .manga-list-categories,
        input {
            border: 2px #000 solid;
            box-shadow: 0 1px 3px 0 #fff;
            border-radius: 5px;
            background-color: #fff;
            color: #262626;
            cursor: pointer;
            margin: 5px;
            padding: 10px;
        }

        .manga-list-categories,
        input[type="submit"],
        input[type="number"] {
            width: 850px;
            text-align: center;
            text-transform: uppercase;
        }

        .container {
            background-color: #f7f7f7;
            border-radius: 5px;
            width: 950px;
            margin: 30px auto 10px auto;
            padding: 5px;
        }

        .container,
        .manga-cover,
        .manga-categories,
        .manga-chapters,
        .manga-score,
        .manga-status,
        .manga-synopsis {
            border: 3px #fff solid;
            box-shadow: 0 1px 3px 0 #0000004d;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode';
        }

        .manga-list {
            list-style: none;
        }

        .manga-box {
            min-height: 220px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .manga-cover {
            border-radius: 10px;
            float: left;
            height: 238px;
            width: 176px;
            margin-right: 20px;
        }

        .manga-info {
            color: #656565;
            display: inline-block;
            width: 660px;
        }

        .manga-info span {
            margin-bottom: 10px;
        }

        .manga-title {
            font-size: 26px;
        }

        .manga-categories,
        .manga-chapters,
        .manga-score,
        .manga-status,
        .manga-synopsis {
            background-color: #fff;
            padding: 6px;
            text-align: justify;
        }

        .manga-filter-min,
        .manga-filter-max {
            padding-left: 5px;
        }

        .manga-filter-min label {
            padding-right: 10px;
        }

        .manga-filter-max label {
            padding-right: 6px;
        }

        .manga-filter-min input[type="number"],
        .manga-filter-max input[type="number"] {
            width: 60px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="contents">
            <ul class="manga-list">
                <form action="index.php" method="POST">
                    <select class="manga-list-categories" name="category">
                        <option value="-1">Selecione um gênero:</option>
                        <option value="23">Ação</option>
                        <option value="61">Adulto</option>
                        <option value="36">Artes Marciais</option>
                        <option value="60">Bara</option>
                        <option value="25">Carros</option>
                        <option value="26">Comédia</option>
                        <option value="27">Demência</option>
                        <option value="28">Demônios</option>
                        <option value="57">Doujinshi</option>
                        <option value="29">Drama</option>
                        <option value="18">Ecchi</option>
                        <option value="46">Escolar</option>
                        <option value="49">Espacial</option>
                        <option value="50">Esportes</option>
                        <option value="30">Fantasia</option>
                        <option value="10">Fanzine</option>
                        <option value="58">Gastronomia</option>
                        <option value="6">Generico</option>
                        <option value="31">Harém</option>
                        <option value="17">Hentai</option>
                        <option value="32">Histórico</option>
                        <option value="33">Horror</option>
                        <option value="15">HQ</option>
                        <option value="34">Infantil</option>
                        <option value="59">Isekai</option>
                        <option value="8">Jogos</option>
                        <option value="9">Josei</option>
                        <option value="16">Light Novel</option>
                        <option value="35">Magia</option>
                        <option value="37">Mechas</option>
                        <option value="38">Militar</option>
                        <option value="40">Misterio</option>
                        <option value="39">Música</option>
                        <option value="11">One Shot</option>
                        <option value="41">Paródia</option>
                        <option value="42">Policial</option>
                        <option value="43">Psicológico</option>
                        <option value="44">Romance</option>
                        <option value="45">Samurai</option>
                        <option value="3">Seinen</option>
                        <option value="2">Shoujo</option>
                        <option value="22">Shoujo Ai</option>
                        <option value="1">Shounen</option>
                        <option value="20">Shounen Ai</option>
                        <option value="48">Slice of Life</option>
                        <option value="52">Sobrenatural</option>
                        <option value="51">Super Poderes</option>
                        <option value="53">Suspense</option>
                        <option value="54">Vampiros</option>
                        <option value="56">Webtoon</option>
                        <option value="19">Yaoi</option>
                        <option value="21">Yuri</option>
                        <input type="submit" value="Carregar Mangás">
                    </select>
                    <div class="manga-filter-min">
                        <label>Número Mínimo de Capítulos: </label>
                        <input type="number" name="min-chaps" value="1">
                    </div>
                    <div class="manga-filter-max">
                        <label>Número Máximo de Capítulos: </label>
                        <input type="number" name="max-chaps" value="9999">
                    </div>
                </form>
                <?php
                if ($_POST) {
                    $category = $_POST["category"];
                    if ($category != -1) {
                        $cache_category = $category;
                        echo load_manga_list(1, $category, $_POST["min-chaps"], $_POST["max-chaps"]);
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</body>

</html>