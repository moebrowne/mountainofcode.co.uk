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

            if (e.target.innerText.toLowerCase() === 'up') {
                window.scrollBy(0, -25)
            }

            if (e.target.innerText.toLowerCase() === 'down') {
                window.scrollBy(0, 25)
            }
        }
    );