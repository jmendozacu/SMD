// JS for Gallery Filters
jQuery.noConflict();

(function( $ ) {
    
    $(document).ready(function () {

        // Variables
        var $filters = $('.tag-category'),
            $selectedFilters = $('#selectedTags'),
            filterSelector = '.tag-link',
            hideClass = 'no-display',
            removeTemplate = '<span class="remove">X</span>',
            $grid = $('.gallery-wrapper > .row'),
            masonryConfig = {
                itemSelector: '.grid-item-wrapper',
                percentPosition: true,
                gutter: 0
            },
            isotopeConfig = {
                itemSelector: '.grid-item-wrapper',
                // layout mode options
                masonry: masonryConfig
            },
            $noItemsText = $('#noImagesMatch');
        
        // Init masonry after images have loaded
        $grid.imagesLoaded(function(){
            $grid.isotope(isotopeConfig);
        });
        
        // Functions
        function handleFilter($filter) {
            var $clone = $filter.clone(),
                category = $filter.data('category'),
                $selectedFilterGroup = $selectedFilters.find('#selectedCategory' + category);
            addSelectedFilter($clone, $selectedFilterGroup);
            hideFilter($filter);
        }

        function handleSelectedFilter($selectedFilter) {
            var $selectedFilterGroup = $selectedFilter.parent(),
                category = $selectedFilter.data('category'),
                filter = $selectedFilter.data('tag-id'),
                $filterGroup = $('#tagCategory' + category),
                $filter = $filterGroup.find(filterSelector + '[data-tag-id= ' + filter + ']');
            removeSelectedFilter($selectedFilter);
            showFilter($filter);
        }

        function addSelectedFilter($clone, $selectedFilterGroup) {
            // Insert cloned filter into selected filter group
            $clone.appendTo($selectedFilterGroup);
            // Modify and return cloned filter
            return $clone
                .addClass('selected-tag')
                .append($(removeTemplate));
        }

        function hideFilter($filter) {
            // Hide original filter
            return $filter.addClass(hideClass);
        }

        function removeSelectedFilter($selectedFilter) {
            // Remove selected filter
            return $selectedFilter.remove();
        }

        function showFilter($filter) {
            // Show original filter
            return $filter.removeClass(hideClass);
        }

        function updateSelectedFilters() {
            var $selectedFilterGroups = $selectedFilters.find('.selected-tag-category'),
                hasFilter = false;
            // Loop over selected filter groups
            $.each($selectedFilterGroups, function() {
                var $selectedFilterGroup = $(this);
                // Check selected filter group for filters
                if($selectedFilterGroup.children(filterSelector).length > 0) {
                    // Show filter group
                    $selectedFilterGroup.removeClass(hideClass);
                    // Set flag
                    hasFilter = true;
                    // Return
                    return $selectedFilterGroup;
                }
                // Hide selected filter group as no filters
                return $selectedFilterGroup.addClass(hideClass);
            });
            // Check if at least one filter selected
            if (hasFilter) {
                // Show selected filters
                return $selectedFilters.removeClass(hideClass);
            }
            // Hide selected filters
            return $selectedFilters.addClass(hideClass);
        }

        function filterItems() {
            var $item = $(this),
                itemCategories = $item.data('tag-ids').split(','),
                filterCategories = getCurrentFilterCategories();
            return setContainsSubset(itemCategories, filterCategories);
        }

        function getCurrentFilterCategories() {
            var $currentFilters = $selectedFilters.find(filterSelector),
                filterCategories = [];
            $.each($currentFilters, function() {
                return filterCategories.push($(this).data('tag-id').toString());
            });
            return filterCategories;
        }
        
        function setContainsSubset(set, subSet) {
            var matches = [];
            // Loop over each item in subset
            $.each(subSet, function(){
                // Check whether subset item in set
                matches.push($.inArray(this.valueOf(), set) > -1);
            });
            // Check whether any of the matches missed
            return $.inArray(false, matches) > -1 ? false : true;
        }

        function handleNoItems(items) {
            if (items.length > 0) {
                // Hide no items text
                return $noItemsText.addClass(hideClass);
            }
            // Show no items text
            return $noItemsText.removeClass(hideClass);
        }

        // Listen for filter events
        $filters
            .on('click', filterSelector,
                function(event) {
                    var $filter = $(event.currentTarget);
                    handleFilter($filter);
                    updateSelectedFilters();
                    // Update gallery items
                    $grid.isotope({ filter: filterItems });
                }
            );
        // Listen for selected filter events
        $selectedFilters
            .on('click', filterSelector,
                function(event) {
                    var $selectedFilter = $(event.currentTarget);
                    handleSelectedFilter($selectedFilter);
                    updateSelectedFilters();
                    // Update gallery items
                    $grid.isotope({ filter: filterItems });
                }
            )
            .on('click', '#resetLink',
                function(event) {
                     var $currentFilters = $selectedFilters.find(filterSelector);
                    // Loop over current selected filters
                    $.each($currentFilters, function() {
                        handleSelectedFilter($(this));
                    });
                    updateSelectedFilters();
                    // Update gallery items
                    $grid.isotope({ filter: filterItems });
                }
            );
        // Listen Isotope events on the gallery
        $grid
            .on('arrangeComplete',
                function( event, filteredItems ) {
                    handleNoItems(filteredItems);
                }
            );
        
        // Move tag navigtion above gallery on mobile
        var $beInspired = $('.cms-be-inspired'),
            $galleryWrapper = $beInspired.find('.main .gallery-wrapper'),
            $tagNavigation = $beInspired.find('.tag-navigation'),
            $sidebarPlaceholder = $('<div id="js-tag-navigation-placeholder"/>').insertBefore($tagNavigation),
            $mainPlaceholder = $('<div id="js-tag-gallery-wrapper-placeholder"/>').insertBefore($galleryWrapper);
        
        enquire.register('screen and (max-width: 770px)', {
            match: function () {
                $tagNavigation
                    .detach()
                    .insertBefore($mainPlaceholder);
            },
            unmatch: function () {
                $tagNavigation
                    .detach()
                    .insertBefore($sidebarPlaceholder);
            }
        });
    });

})( jQuery );
