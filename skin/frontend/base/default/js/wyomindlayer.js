var WyomindLayer = Class.create();
WyomindLayer.prototype = {
    initialize: function(options) {
        this.options = Object.extend({
            topSelector:        '.wyomind-layer-top',
            leftSelector:       '.col-left',
            rightSelector:      '.col-right',
            contentSelector:    '.category-products',
            scrollSelector:     '.col-left',
            enableAjax:         true,
            enableAutoScroll:   true,
            enableAjaxToolbar:  true,
            enableHistory:      true,
            onUpdateComplete:   function() {}
        }, options || {});

        this.loading = false;

        if (typeof(History) === 'undefined' || !this.options.enableAjax) {
            this.options.enableHistory = false;
        }

        var o = this.options;

        this.handlePriceSliders();

        if (o.enableHistory && this.getLeftElement() && this.getContentElement()) {
            document.observe('dom:loaded', function() {
                var topEl = this.getTopElement();
                var leftEl = this.getLeftElement();
                var rightEl = this.getRightElement();
                var contentEl = this.getContentElement();
                this.saveState(
                    document.location.href,
                    topEl ? topEl.innerHTML : '',
                    leftEl ? leftEl.innerHTML : '',
                    rightEl ? rightEl.innerHTML : '',
                    contentEl ? contentEl.innerHTML : '',
                    document.title
                );
            }.bind(this));
        }

        document.observe('click', function(e) {
            var el = e.element();
            if (el.hasClassName('show-hidden')) {
                e.stop();
                el.up(1).select('li.hideable').invoke('toggleClassName', 'no-display');
                el.up(1).select('.show-hidden').invoke('toggleClassName', 'no-display');
            } else if (e.findElement('.block-layered-nav a') ||
                       e.findElement('.checkbox-filter') ||
                       e.findElement('.toolbar a') && o.enableAjaxToolbar)
            {
                var url;
                if (el.tagName == 'INPUT') {
                    url = el.value;
                } else if (!el.href && el.up('a')) {
                    el = el.up('a');
                }
                if (!url && el.href) {
                    url = el.href;
                    e.stop();
                    if (el.up('li')) {
                        var input = el.up('li').down('input');
                        if (input) {
                            input.checked = !input.checked;
                        }
                    }
                }
                if (url) {
                    this.handleLayer(url);
                }
            }
        }.bind(this));

        document.observe('change', function(e) {
            var el = e.element();
            if (el.hasClassName('dropdown-filter')) {
                var url = el.getValue();
                if (url) {
                    this.handleLayer(url);
                }
            }
        }.bind(this));

        if (o.enableHistory) {
            History.Adapter.bind(window, 'statechange', function() {
                var state   = History.getState();
                var data    = state.data;
                this.updateContent(data.top, data.left, data.right, data.content);
                this.handlePriceSliders();
            }.bind(this));
        }

        Event.observe(window, 'resize', function() {
            this.handlePriceSliders();
        }.bind(this));

        Event.observe(window, 'load', function() {
            $$('.block-layered-nav dt').invoke('observe', 'click', function () {
                this.handlePriceSliders();
            }.bind(this));
        }.bind(this));
    },
    getTopElement: function() {
        return $$(this.options.topSelector).shift();
    },
    getLeftElement: function() {
        return $$(this.options.leftSelector).shift();
    },
    getRightElement: function() {
        return $$(this.options.rightSelector).shift();
    },
    getContentElement: function() {
        return $$(this.options.contentSelector).shift();
    },
    getScrollElement: function() {
        return $$(this.options.scrollSelector).shift();
    },
    setCurrentUrl: function(url) {
        this.options.currentUrl = url;
    },
    saveState: function(url, top, left, right, content, title) {
        if (this.options.enableHistory) {
            var state = {
                top: top,
                left: left,
                right: right,
                content: content
            };
            History.pushState(state, title, url);
        }
    },
    scroll: function() {
        var scrollEl = this.getScrollElement();
        if (this.options.enableAutoScroll && scrollEl) {
            new Effect.ScrollTo(scrollEl);
        }
    },
    updateContent: function(top, left, right, content) {
        var topEl = this.getTopElement();
        var leftEl = this.getLeftElement();
        var rightEl = this.getRightElement();
        var contentEl = this.getContentElement();
        if (topEl) {
            topEl.update(top);
        }
        if (leftEl) {
            leftEl.update(left);
        }
        if (rightEl) {
            rightEl.update(right);
        }
        if (contentEl) {
            contentEl.update(content);
        }
    },
    handleLayer: function(url) {
        if (document.location.href == url) {
            return;
        }
        if (!this.options.enableAjax) {
            setLocation(url);
        } else {
            if (this.loading) {
                return;
            }
            new Ajax.Request(url, {
                method: 'get',
                onCreate: function() {
                    this.loading = true;
                    if ($('wyomind-layer-overlay')) {
                        $('wyomind-layer-overlay').show();
                    }
                }.bind(this),
                onSuccess: function(response) {
                    var result  = response.responseJSON;
                    if (result.content) {
                        this.updateContent(result.top, result.left, result.right, result.content);
                        this.saveState(url, result.top, result.left, result.right, result.content, result.title);
                        this.handlePriceSliders();
                        this.scroll();
                    }
                    this.setCurrentUrl(url);
                }.bind(this),
                onComplete: function(response) {
                    if ($('wyomind-layer-overlay')) {
                        $('wyomind-layer-overlay').hide();
                    }
                    this.loading = false;
                    this.options.onUpdateComplete.call(this);
                }.bind(this),
                onFailure: function() {
                    setLocation(url);
                }
            });
        }
    },
    handlePriceSliders: function() {
        $$('.layer-slider').each(function(slider) {
            this.handlePriceSlider(slider);
        }.bind(this));
    },
    handlePriceSlider: function(slider) {
        if (slider) {
            var self = this;
            var handles = slider.select('.handle');
            var requestVar = slider.select('.request-var')[0].value;
            var priceMin = parseInt(slider.select('.price-min')[0].value);
            var priceMax = parseInt(slider.select('.price-max')[0].value);
            var priceValueMin = parseInt(slider.select('.price-value-min')[0].value);
            var priceValueMax = parseInt(slider.select('.price-value-max')[0].value);

            if (typeof(WyomindPriceSlider) !== 'undefined') {
                WyomindPriceSlider.dispose();
            }

            var WyomindPriceSlider = new Control.Slider(handles, slider.select('.price-slider')[0], {
                range: $R(priceMin, priceMax),
                sliderValue: [priceValueMin, priceValueMax],
                restricted: true,
                spans: slider.select('.span'),
                onSlide: function(v) {
                    if (!isNaN(v[0])) {
                        slider.select('.price-range span.price')[0].update(formatCurrency(v[0], self.options.priceFormat, false));
                    }
                    if (!isNaN(v[1])) {
                        slider.select('.price-range span.price')[1].update(formatCurrency(v[1], self.options.priceFormat, false));
                    }
                },
                onChange: function(v) {
                    var min = v[0].toFixed();
                    if (min == 0 || isNaN(min) || min <= this.range.start) {
                        min = '';
                    }
                    var max = v[1].toFixed();
                    if (max >= this.range.end || isNaN(max)) {
                        max = '';
                    }

                    self.handleLayer(self.buildPriceUrl(requestVar, min, max));
                }
            });

            var priceInputs = slider.select('.price-range input.input-text');
            priceInputs.each(function(input) {
                input.observe('keyup', function(event) {
                    if (event.keyCode == Event.KEY_RETURN) {
                        // Handle mininmum price input
                        var min = priceValueMin;
                        if (typeof(priceInputs[0]) !== 'undefined' && priceInputs[0].value != '') {
                            min = parseInt(priceInputs[0].value);
                            if (min == 0 || isNaN(min) || min <= WyomindPriceSlider.range.start) {
                                min = priceMin;
                                if (input == priceInputs[0] && min === priceValueMin) {
                                    new Effect.Pulsate(priceInputs[0], { pulses: 3, duration: 0.5 });
                                }
                            }
                        }
                        // Handle maximum price input
                        var max = priceValueMax;
                        if (typeof(priceInputs[1]) !== 'undefined' && priceInputs[1].value != '') {
                            max = parseInt(priceInputs[1].value);
                            if (max >= WyomindPriceSlider.range.end || isNaN(max)) {
                                max = priceMax;
                                if (input == priceInputs[1] && max === priceValueMax) {
                                    new Effect.Pulsate(priceInputs[1], { pulses: 3, duration: 0.5 });
                                }
                            }
                        }
                        if (min !== priceValueMin || max !== priceValueMax) {
                            self.handleLayer(self.buildPriceUrl(requestVar, min, max));
                        }
                    }
                });
            });

            // Handle mouseover on slider to modify the closest handle on click
            slider.observe('mousemove', function(event) {
                var posHandleL = handles[0].cumulativeOffset().left;
                var posHandleR = handles[1].cumulativeOffset().left;
                var posPointer = Event.pointerX(event);
                if (posPointer > posHandleR || posPointer > ((posHandleR + posHandleL) / 2)) {
                    WyomindPriceSlider.activeHandle = handles[1];
                    WyomindPriceSlider.activeHandleIdx = 1;
                } else {
                    WyomindPriceSlider.activeHandle = handles[0];
                    WyomindPriceSlider.activeHandleIdx = 0;
                }
                WyomindPriceSlider.updateStyles();
            });
        }
    },
    buildPriceUrl: function(requestVar, min, max) {
        min = parseInt(min);
        max = parseInt(max);
        if (!isNaN(min) && !isNaN(max) && max < min) {
            var badMin = min;
            min = max;
            max = badMin;
        }
        if (isNaN(min)) {
            min = '';
        }
        if (isNaN(max)) {
            max = '';
        }
        var param = requestVar + '=' + min + '-' + max;
        var pattern = '(&|\\?)' + requestVar + '=\\d*-\\d*';
        var url = this.options.currentUrl;
        if (!min && !max) {
            url = url.replace(new RegExp(pattern), '');
        } else if (url.match(new RegExp(pattern))) {
            url = url.replace(new RegExp(pattern), '$1' + param);
        } else {
            url += url.match(new RegExp('\\?')) ? '&' : '?';
            url += param;
        }
        url = url.replace(new RegExp('(&|\\?)p=\\d+(&|\\?)'), '$1'); // Remove page filter

        return url.replace(/[&\?]+$/g, '');
    },
    adaptHeight: function(selector) {
        var maxHeight = 0;
        var top = null;
        var offsets = {};
        var items = [];
        $$(selector).each(function(item, i) {
            item.setStyle('height', 'auto');
            offsets = item.getBoundingClientRect();
            if (top !== offsets.top) {
                this.setElementsHeight(items, maxHeight);
                items = [];
                maxHeight = 0;
            }
            if (item.offsetHeight > maxHeight) {
                maxHeight = item.offsetHeight;
            }
            items.push(item);
            top = offsets.top;
            this.setElementsHeight(items, maxHeight);
        }.bind(this));
    },
    setElementsHeight: function(elements, height) {
        if (height) {
            elements.each(function(el) {
                el.style.height = height + 'px';
            });
        }
    }
};