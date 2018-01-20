jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
jQuery(function() {
    var match_found_title = false;
    jQuery('#search_faq').keyup(function() {
        jQuery("#accordion h3.faq_question, #accordion div.faq_answer").each(function() {
            var $this = jQuery(this);
            if ($this.is(":contains('" + jQuery('#search_faq').val() + "')")) {
                match_found_title = $this.is('h3');
                //$this.show();
                //jQuery('#' + $this.attr('rel')).show();
                jQuery('.faqitem-' + $this.attr('rel')).show();
            }
            else {
                if (!match_found_title) {
                    //$this.hide();
                    //jQuery('#' + $this.attr('rel')).hide();
                    jQuery('.faqitem-' + $this.attr('rel')).hide();
                }
                else {
                    match_found_title = false;
                }
            }
        });
    });
    jQuery(document).on('click', '.faq_vote', function() {
        var faq_box = jQuery(this).parent().parent();
        var url_target = jQuery(this).attr('href');
        var vote_value = jQuery(this).hasClass('faq_useful') ? 1 : -1;
        var faqId = faq_box.find('input.faqId').val();
        faq_box.append('<span class="loading">Loading</span>');

        jQuery.post(url_target, {faqId: faqId, vote: vote_value},
        function(data) {
            faq_box.html(data);
        })
                .fail(function(xhr, textStatus, errorThrown) {
                    alert(xhr.responseText);
                })
        return false;
    });
});