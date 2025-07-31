<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>今日のコーデ</title>
</head>
<body>
    <h1>今日のおすすめコーデ</h1>
    <div class="continer">
        <h2>天気情報取得</h2>
        <form action="" method="get">
            <label for="city">都市名を入力してください:</label>
            <input type="text" id="city" name="city" placeholder="例: Tokyo" value="<?php echo htmlspecialchars($_GET['city'] ?? ''); ?>">
            <button type="submit">天気を見る</button>
        </form>

        <?php
        //OpenWeatherMap APIキー
        $apikey = "0c2462eac73772241a16d88bcc013e34";
        //フォームから都市名を取得
        $city = "";
        if (isset($_GET["city"]) && !empty($_GET["city"])) {
            $city = htmlspecialchars($_GET['city']); //XSS対策としてエスケープ処理
        }

        //都市名が入力されている場合のみAPIリクエストを実行
        if(!empty($city)){
            $units = "metric"; //摂氏温度
            
            //APIエンドポイントの構築
            $apiurl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apikey}&units={$units}&lang=ja"; //lang=jaで日本語の天気情報を取得

            //APIリクエストを実行
            // cURLを使用する方がより安定していますが、簡単なGETリクエストであればfile_get_contentsでも可
            $response = file_get_contents($apiurl);

            // レスポンスが取得できたか確認と処理
            if ($response === FALSE) {
                echo "<p>天気情報の取得に失敗しました。ネット接続と、都市名を確認してください。</p>";
            } else {
                // JSONデータをデコード
                $weatherData = json_decode($response, true);

                // デバッグ用に生データを表示
                // echo "<pre>";
                // print_r($weatherData);
                // echo "</pre>";

                // 天気情報の表示
                if ($weatherData && $weatherData["cod"] == 200) {//cod200は成功を意味します
                    echo "<div class='weather-info'>";
                    echo "<h2>{$weatherData['name']} の現在の天気</h2>";
                    echo "<p>天気: " . ($weatherData['weather'][0]['description'] ?? 'N/A') . "</p>";
                    echo "<p>気温: " . ($weatherData['main']['temp'] ?? 'N/A') . " °C</p>";
                    echo "<p>体感温度: " . ($weatherData['main']['feels_like'] ?? 'N/A') . " °C</p>";
                    echo "<p>湿度: " . ($weatherData['main']['humidity'] ?? 'N/A') . " %</p>";
                    echo "</div>";
                } else {
                    $errorMessage = "天気情報の取得に失敗しました。";
                    if (isset($weatherData['cod']) && $weatherData['cod'] == 404) {
                        $errorMessage .= "指定された都市が見つかりません。";
                    } elseif (isset($weatherData['message'])) {
                        $errorMessage .= "エラーメッセージ: " . $weatherData['message'];
                    }
                    echo "<p class='error-message'>{$errorMessage}</p>";
}
            }
        }elseif (isset($_GET['city'])) { // cityパラメータは存在するが空の場合
            echo "<p class='error-message'>都市名を入力してください。</p>";
        }
        ?>
    </div>
</body>
</html>