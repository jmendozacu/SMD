document.observe("dom:loaded", function () {
    Event.on(
        document.body,
        'click',
        '#amprivacy-checkbox a, #amprivacy-popup .cross',
        togglePrivacyPopup
    );

    $$('[data-role="accept-policy"]')[0].observe('click', acceptPolicy);

    function togglePrivacyPopup(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
            $('amprivacy-popup').toggle();
        }
    }

    function acceptPolicy(e) {
        var checkbox = $$('#amprivacy-checkbox input[type="checkbox"]')[0];
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));

        togglePrivacyPopup(e);
    }
});
