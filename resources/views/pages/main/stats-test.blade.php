<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
    // Добавляет header ко всем fetch-запросам на странице
    (function() {
      const originalFetch = window.fetch;
      window.fetch = function(resource, config = {}) {
        config.headers = config.headers || {};
        // Добавьте нужный header, например X-Custom-Header
        config.headers['X-My-Header'] = 'MyHeaderValue';
        return originalFetch(resource, config);
      };
    })();
    </script>
</head>
<body>


  <div id="rankeval-widget"></div>


<script src="https://cdn.rankeval.gg/integration/latest/rankeval-widget.js"></script>

</body>
</html>