<script src="/assets/hexGrid.js"></script>
<script>
    window.requestIdleCallback(() => hexGrid.init(document.getElementById("hexGridEl")));
</script>

<script>
document
    .addEventListener(
        'click',
        (e) => {
            if (e.target.nodeName !== 'KBD') {
                return;
            }

            if (e.target.innerText.toLowerCase() === 'end') {
                window.scrollBy(0, window.innerHeight)
            }
        }
    );
</script>
</body>
</html>