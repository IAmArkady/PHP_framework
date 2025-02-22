<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="js/index.js"></script>
    <script src="js/tabs.js"></script>
</head>
<body>
<form action="{{ SRC\Route::routeByName('checkLanguage') }}" class="container">
    <h1>Проверка языка</h1>

    <div class="tabs">
        <button class="active" data-tab="input" type="button">Поле ввода</button>
        <button id="history" data-tab="history" type="button">История ввода</button>
    </div>

    <div class="tab-content input active">
        <div class="textarea-wrapper">
            <div id="highlight"></div>
            <textarea class="textarea" id="textarea" spellcheck="false"></textarea>
        </div>
        <button class="check-button" type="button">
            Проверить
        </button>
    </div>

    <div class="tab-content history">
    </div>
</form>

</body>
</html>
