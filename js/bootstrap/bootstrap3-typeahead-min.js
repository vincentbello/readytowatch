! function(e) {
    "use strict";
    var t = function(t, n) {
        this.$element = e(t), this.options = e.extend({}, e.fn.typeahead.defaults, n), this.matcher = this.options.matcher || this.matcher, this.sorter = this.options.sorter || this.sorter, this.select = this.options.select || this.select, this.autoSelect = "boolean" == typeof this.options.autoSelect ? this.options.autoSelect : !0, this.highlighter = this.options.highlighter || this.highlighter, this.updater = this.options.updater || this.updater, this.source = this.options.source, this.$menu = e(this.options.menu), this.shown = !1, this.listen(), this.showHintOnFocus = "boolean" == typeof this.options.showHintOnFocus ? this.options.showHintOnFocus : !1
    };
    t.prototype = {
        constructor: t,
        select: function() {
            var active = this.$menu.find('.active');
            if (active.length) {
                var type = active.data('type'),
                id = active.data('id'),
                l = type + '/' + id;
            }
            return l ? void(window.location = l) : (this.autoSelect, this.hide(), "nav-search" == this.$element.attr("id") ? e("#nav-form").submit() : e("#home-search").submit())
        },
        updater: function(e) {
            return e.replace(/[<].+[>] /, "").replace(/ [(]\d{4}[)]/, "")
        },
        setSource: function(e) {
            this.source = e
        },
        show: function() {
            var t, n = e.extend({}, this.$element.position(), {
                height: this.$element[0].offsetHeight
            });
            return t = "function" == typeof this.options.scrollHeight ? this.options.scrollHeight.call() : this.options.scrollHeight, this.$menu.insertAfter(this.$element).css({
                top: n.top + n.height + t,
                left: n.left
            }).show(), this.shown = !0, this
        },
        hide: function() {
            return this.$menu.hide(), this.shown = !1, this
        },
        lookup: function(t) {
            var n;
            return this.query = "undefined" != typeof t && null !== t ? t : this.$element.val() || "", this.query.length < this.options.minLength ? this.shown ? this.hide() : this : (n = e.isFunction(this.source) ? this.source(this.query, e.proxy(this.process, this)) : this.source, n ? this.process(n) : this)
        },
        process: function (items) {
            var that = this;
            items = $.grep(items, function (item) {
              return that.matcher(item.title);
            });
    
            items = this.sorter(items);
        
            if (!items.length) {
              return this.shown ? this.hide() : this;
            }
        
            if (this.options.items == 'all') {
              return this.render(items).show();
            } else {
              return this.render(items.slice(0, this.options.items)).show();
            }
        },
        matcher: function(e) {
            console.log(e);
            var e = e.toLowerCase(), q = this.query.toLowerCase()
            return (e.indexOf(q) == 0) || (e.indexOf(" " + q) > -1);
        },
        sorter: function (items) {
            // var beginswith = []
            //   , caseSensitive = []
            //   , caseInsensitive = []
            //   , item;
    
            // while ((item = items.shift())) {
            //   if (!item.title.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item);
            //   else if (~item.title.indexOf(this.query)) caseSensitive.push(item);
            //   else caseInsensitive.push(item);
            // }
            
            // var arr = beginswith.concat(caseSensitive, caseInsensitive);
            return items;
        },
        highlighter: function(e) {
            var t = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
            return e.replace(new RegExp("(" + t + ")", "ig"), function(e, t) {
                return "<strong>" + t + "</strong>"
            })
        },
        render: function (items) {
            var that = this;
            items = $(items).map( function (i, item) {
                i = $(that.options.item).data('id', item.id).data('type', 'movie');
                i.find('a').append(that.highlighter(item.title) + " <span class='year'>" + item.year + "</span>");
                if (item.img_link.length)
                    i.find('img').attr('src', 'http://image.tmdb.org/t/p/w185' + item.img_link);
                return i[0];
            });

            if (this.autoSelect) {
                items.first().addClass('active');
            }
            this.$menu.html(items);
            return this;
        },
        next: function() {
            var t = this.$menu.find(".active").removeClass("active"),
                n = t.next();
            if (n.length) {
                while (n.hasClass("header")) n = n.next()
            }
            n.length || (n = e(this.$menu.find("li:not(.header)")[0])), n.addClass("active")
        },
        prev: function() {
            var e = this.$menu.find(".active").removeClass("active"),
                t = e.prev();
            t.length || (t = this.$menu.find("li").last()), t.addClass("active")
        },
        listen: function() {
            this.$element.on("focus", e.proxy(this.focus, this)).on("blur", e.proxy(this.blur, this)).on("keypress", e.proxy(this.keypress, this)).on("keyup", e.proxy(this.keyup, this)), this.eventSupported("keydown") && this.$element.on("keydown", e.proxy(this.keydown, this)), this.$menu.on("click", e.proxy(this.click, this)).on("mouseenter", "li", e.proxy(this.mouseenter, this)).on("mouseleave", "li", e.proxy(this.mouseleave, this))
        },
        destroy: function() {
            this.$element.data("typeahead", null), this.$element.off("focus").off("blur").off("keypress").off("keyup"), this.eventSupported("keydown") && this.$element.off("keydown"), this.$menu.remove()
        },
        eventSupported: function(e) {
            var t = e in this.$element;
            return t || (this.$element.setAttribute(e, "return;"), t = "function" == typeof this.$element[e]), t
        },
        move: function(e) {
            if (this.shown) {
                switch (e.keyCode) {
                    case 9:
                    case 13:
                    case 27:
                        e.preventDefault();
                        break;
                    case 38:
                        e.preventDefault(), this.prev();
                        break;
                    case 40:
                        e.preventDefault(), this.next()
                }
                e.stopPropagation()
            }
        },
        keydown: function(t) {
            this.suppressKeyPressRepeat = ~e.inArray(t.keyCode, [40, 38, 9, 13, 27]), this.shown || 40 != t.keyCode ? this.move(t) : this.lookup("")
        },
        keypress: function(e) {
            this.suppressKeyPressRepeat || this.move(e)
        },
        keyup: function(e) {
            switch (e.keyCode) {
                case 40:
                case 38:
                case 16:
                case 17:
                case 18:
                    break;
                case 9:
                case 13:
                    if (!this.shown) return;
                    this.select();
                    break;
                case 27:
                    if (!this.shown) return;
                    this.hide();
                    break;
                default:
                    this.lookup()
            }
            e.stopPropagation(), e.preventDefault()
        },
        focus: function() {
            this.focused || (this.focused = !0, (0 === this.options.minLength && !this.$element.val() || this.options.showHintOnFocus) && this.lookup())
        },
        blur: function() {
            this.focused = !1, !this.mousedover && this.shown && this.hide()
        },
        click: function(e) {
            e.stopPropagation(), e.preventDefault(), this.select(), this.$element.focus()
        },
        mouseenter: function(t) {
            this.mousedover = !0, this.$menu.find(".active").removeClass("active"), e(t.currentTarget).addClass("active")
        },
        mouseleave: function() {
            this.mousedover = !1, !this.focused && this.shown && this.hide()
        }
    };
    var n = e.fn.typeahead;
    e.fn.typeahead = function(n) {
        var r = arguments;
        return this.each(function() {
            var o = e(this),
                u = o.data("typeahead"),
                a = "object" == typeof n && n;
            u || o.data("typeahead", u = new t(this, a)), "string" == typeof n && (r.length > 1 ? u[n].apply(u, Array.prototype.slice.call(r, 1)) : u[n]())
        })
    }, e.fn.typeahead.defaults = {
        source: [],
        items: 8,
        menu: '<ul class="typeahead dropdown-menu"></ul>',
        item: '<li><a><img height="60" src="images/no_image_found.png"></a></li>',
        itemHeader: '<li class="header"><a href="#"></a></li>',
        minLength: 1,
        scrollHeight: 0,
        autoSelect: !0
    }, e.fn.typeahead.Constructor = t, e.fn.typeahead.noConflict = function() {
        return e.fn.typeahead = n, this
    }, e(document).on("focus.typeahead.data-api", '[data-provide="typeahead"]', function() {
        var t = e(this);
        t.data("typeahead") || t.typeahead(t.data())
    })
}(window.jQuery)