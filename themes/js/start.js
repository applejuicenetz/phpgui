document.addEventListener('DOMContentLoaded', () => {
    setInterval(function () {
        window.fetch("index.php?site=ajax")
            .then(response => response.text())
            .then(data => {
                document.getElementById("load_posts").innerHTML = data;
            });
    }, 1000);
});