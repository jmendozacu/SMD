// JS for Gallery Filters

jQuery.noConflict();

(function( $ ) {

    var selectedTags = {};

    $(document).ready(function () {

        $('.tag-link').each(function () {
            $(this).on('click', function () {
                $(this).addClass('selected');
                if($(this).data('category') in selectedTags) {
                    if(selectedTags[$(this).data('category')].indexOf($(this).data('tag-id').toString()) >= 0){
                        return;
                    }
                    selectedTags[$(this).data('category')].push($(this).data('tag-id').toString());
                }else {
                    selectedTags[$(this).data('category')] = [];
                    selectedTags[$(this).data('category')].push($(this).data('tag-id').toString());
                }
                handleImageDisplays();
                addFilter($(this));
                initMasonry();
            });
        });

    });

    $(document).on('click', '.selected-tag', function() {
        var tag = $(this);
        var index = selectedTags[tag.data('category')].indexOf(tag.data('tag-id').toString());
        if(index > -1) {
            selectedTags[tag.data('category')].splice(index, 1);
        }
        removeFilter(tag);
    });

    function handleImageDisplays() {
        $('.grid-item-wrapper').each(function() {
            var image = $(this);
            var tags = image.data('tag-ids');
            if(tags) {
                tags = tags.split(',');
                for ( var i = 0; i < tags.length; i++ ) {
                    var flag = false;
                    Object.keys(selectedTags).some(function(k) {
                        if(selectedTags[k].indexOf(tags[i]) >= 0) {
                            flag = true;
                        }
                    });
                    if (flag) {
                        if (image.hasClass('no-display')) {
                            image.removeClass('no-display');
                        }
                        break;
                    } else if (!image.hasClass('no-display')) {
                        image.addClass('no-display');
                    }
                }
            }
        });
    }

    function resetImages() {
        $('.grid-item-wrapper').each(function() {
            var image = $(this);
            if(image.hasClass('no-display')) {
                image.removeClass('no-display');
            }
        });
    }

    function removeFilter(tag) {
        var selectedElWrapper = $('#selectedTags');
        var selectedEl = $('#selectedCategory'+tag.data('category'));
        var index = selectedTags[tag.data('category')].indexOf(tag.data('tag-id').toString());
        if(index > -1) {
            selectedTags[tag.data('category')].splice(index, 1);
        }
        selectedEl.find($('#tag'+tag.data('tag-id')+'')).remove();
        if(selectedTags[tag.data('category')].length == 0) {
            if (selectedEl.hasClass('no-display')) {
                selectedEl.addClass('no-display');
            }
        }
        var flag = false;
        for (var key in selectedTags) {
            if(selectedTags[key].length > 0) {
                flag = true;
            }
        }
        if(!flag) {
            selectedElWrapper.addClass('no-display');
            resetImages();
        }else {
            handleImageDisplays();
        }
        initMasonry();
    }

    function initMasonry() {
        $('.uneven-grid-images').masonry({
            itemSelector: '.grid-item-wrapper',
            percentPosition: true,
            gutter: 10
        });
    }

    function addFilter(tag) {
        var selectedElWrapper = $('#selectedTags');
        var selectedEl = $('#selectedCategory'+tag.data('category'));
        if(tag.data('tag-title')) {
            if(selectedElWrapper.hasClass('no-display')) {
                selectedElWrapper.removeClass('no-display');
            }
            if(selectedEl.hasClass('no-display')) {
                selectedEl.removeClass('no-display');
            }
            var selectedHtml = '';
            if(tag.data('swatch')) {
                selectedHtml = '<img class="selected-tag swatch" data-category="'+tag.data('category')+'" src="'+tag.data('swatch-url')+'" data-tag-id="' + tag.data('tag-id') + '" id="tag' + tag.data('tag-id') + '" width="40" height="40"/>';
            }else {
                selectedHtml = '<p class="selected-tag" data-category="'+tag.data('category')+'" data-tag-id="' + tag.data('tag-id') + '" id="tag' + tag.data('tag-id') + '">' + tag.data('tag-title') + '<span class="remove">x</span></p>';
            }
            selectedEl.append(selectedHtml);
        }
    }

})( jQuery );