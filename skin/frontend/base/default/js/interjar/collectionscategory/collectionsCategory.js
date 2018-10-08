function loadNewProducts(category, pageSize) {
    var buttonClass = '.slick-next-' + category;
    var sliderClass = '.' + category + '-subcategorySlider';
    var sliderEl = jQuery(sliderClass)[0];
    var el = jQuery(buttonClass)[0];
    var clicks = el.dataset.clicks;
    var page = el.dataset.page;
    if (clicks >= 2) {
        jQuery.ajax({
            method: "POST",
            url: "/collections/update/products",
            data: {
                category_id: category,
                bunch: pageSize,
                page: page
            }
        })
            .done(function (response) {
                var slideIndex = jQuery(sliderEl).slick('slickCurrentSlide');
                if (response.html) {
                    var html = response.html;
                    page = parseInt(page) + 1;
                    jQuery(sliderEl).slick('unslick');
                    jQuery(sliderEl).append(html);
                    jQuery(sliderEl).slick({
                        draggable: false,
                        nextArrow: '<button type="button" class="slick-next slick-next-' + category + '" id="slick-next-' + category + '" data-clicks="1" data-page="' + page + '" onclick="loadNewProducts(' + category + ')">Previous</button>',
                        slidesToShow: 6,
                        initialSlide: slideIndex
                    });
                } else {
                    jQuery(sliderEl).slick('unslick');
                    jQuery(sliderEl).slick({
                        draggable: false,
                        slidesToShow: 6,
                        initialSlide: slideIndex
                    });
                }
            });
    } else {
        el.dataset.clicks = parseInt(clicks)+1;
    }
}
