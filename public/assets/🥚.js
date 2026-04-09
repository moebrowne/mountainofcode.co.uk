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