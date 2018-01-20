document.observe('dom:loaded', function () {
    if ($('qop-basket')) {
        $('qop-basket').toggleClassName('expanded');
    }

    if ($('qop-expand')) {
        $('qop-expand').observe('click', function (event) {
            toggleBasket();
            setHeight();
        });
    }

    if ($('qop-pin')) {

        $('qop-pin').observe('click', function (event) {
            var current_width = $('qop-basket').getWidth();

            $('qop-basket').toggleClassName('pinned');

            if ($('qop-basket').hasClassName('pinned')) {
                collapseBasket();
                $('qop-basket').setStyle({
                    'width': current_width.toString() + 'px'
                });
                $('qop-pin').writeAttribute('title', 'Unpin');
            } else {
                expandBasket();
                $('qop-pin').writeAttribute('title', 'Pin');
            }

            setHeight();
        });
    }

    function setHeight() {
        if ($('qop-basket').hasClassName('pinned')) {
            var current_height = $('qop-cart').getHeight();
            if (current_height > 360) {
                $('qop-cart').setStyle({
                    'height': '360px',
                    'overflowY': 'scroll'
                });
            }
        } else {
            $('qop-cart').setStyle({
                'height': 'auto',
                'overflowY': 'auto'
            });
        }
    }

    function toggleBasket() {
        $('qop-basket').toggleClassName('collapsed');
        $('qop-basket').toggleClassName('expanded');
        if ($('qop-basket').hasClassName('collapsed')) {
            $('qop-expand').writeAttribute('title', 'Expand');
        } else {
            $('qop-expand').writeAttribute('title', 'Collapse');
        }
    }

    function expandBasket() {
        $('qop-basket').removeClassName('collapsed');
        $('qop-basket').addClassName('expanded');
        $('qop-expand').writeAttribute('title', 'Collapse');
    }

    function collapseBasket() {
        $('qop-basket').addClassName('collapsed');
        $('qop-basket').removeClassName('expanded');
        $('qop-expand').writeAttribute('title', 'Expand');
    }
});