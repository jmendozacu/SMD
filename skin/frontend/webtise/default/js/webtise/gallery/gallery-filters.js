// JS for Gallery Filters
jQuery.noConflict();

(function( $ ) {

    var selectedTags = {};
    var filteredTags = []; // Flat array of tags to filter

    $(document).ready(function () {

        $('.tag-link').each(function () {
            $(this).on('click', function () {
                var tagId = $(this).data('tag-id').toString();
                $(this).addClass('selected');
                if ($.inArray(tagId, filteredTags) < 0) {
                    filteredTags.push(tagId);
                }
                if($(this).data('category') in selectedTags) {
                    if(selectedTags[$(this).data('category')].indexOf(tagId) >= 0){
                        return;
                    }
                    selectedTags[$(this).data('category')].push(tagId);
                }else {
                    selectedTags[$(this).data('category')] = [];
                    selectedTags[$(this).data('category')].push(tagId);
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

    $(document).on('click', '#resetLink', function(){
        // Reset All Filters
        resetFilter();
    });

    function handleImageDisplays() {
        var atLeastOne = false; // Could potentially use this to show a "No Images match your filters" notice
        $('.grid-item-wrapper').each(function() {
            var image = $(this);
            var tags = image.data('tag-ids');
            if(tags) {
                tags = tags.split(',');
                // In this loop we can loop through filteredTags
                // And make sure each filtered tag is in the images tags
                // If its missing any, we can set a flag an then use that to hide the image
                var flag = false;
                for ( var i = 0; i < filteredTags.length; i++ ) {
                    if ($.inArray(filteredTags[i], tags) < 0) {
                        flag = true;
                    }
                }
                // If we have a flag here then we need to make sure the image is hidden
                if (flag) {
                    if (!image.hasClass('no-display')) {
                        image.addClass('no-display');
                    }
                } else {
                    // If we don't have a flag, make sure its shown
                    if (image.hasClass('no-display')) {
                        image.removeClass('no-display');
                    }
                    atLeastOne = true;
                }
            }
        });
        handleNoImagesNotice(atLeastOne);
    }

    function handleNoImagesNotice(atLeastOne) {
        var noImagesNotice = $('#noImagesMatch');
        if (atLeastOne) {
            if (!noImagesNotice.hasClass('no-display')) {
                noImagesNotice.addClass('no-display');
            }
        } else {
            if (noImagesNotice.hasClass('no-display')) {
                noImagesNotice.removeClass('no-display');
            }
        }
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
        selectedEl.find($('#tag'+tag.data('tag-id'))).remove();
        if(selectedTags[tag.data('category')].length == 0) {
            if (!selectedEl.hasClass('no-display')) {
                selectedEl.addClass('no-display');
            }
        }
        var flag = false;
        for (var key in selectedTags) {
            if(selectedTags[key].length > 0) {
                flag = true;
            }
        }
        removeTagFromFilteredArray(tag.data('tag-id').toString());
        if(!flag) {
            selectedElWrapper.addClass('no-display');
            resetImages();
        } else {
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

    function removeTagFromFilteredArray(tagId) {
        var index = filteredTags.indexOf(tagId);
        if (index > -1) {
            filteredTags.splice(index, 1);
        }
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

    function resetFilter() {
        var elSelectedWrapper = $('#selectedTags');
        $('.selected-tag-category', elSelectedWrapper).each(function(){
            if(!$(this).hasClass('no-display')) {
                var firstThis = $(this);
                var selectedTagsChosen = $('.selected-tag', firstThis);
                selectedTagsChosen.each(function(){
                    var chosenTag = $(this);
                    removeFilter(chosenTag);
                });
            }
        });
    }

})( jQuery );
