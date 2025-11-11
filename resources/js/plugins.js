(function ($) {
   /**
    * Creates namespaced event names from a space-separated string of events.
    * @param {string} NS - The namespace to apply.
    * @param {string} events - Space-separated event names.
    * @returns {string} Space-separated namespaced event names.
    * @example
    * $.ns('myPlugin', 'click mousedown') // Returns "click.myPlugin mousedown.myPlugin"
    */
   $.ns = function (NS, events) {
      return events.split(/\s+/).map(e => `${e}.${NS}`).join(' ');
   };

   /**
    * Creates a debounced function that delays invoking the callback until after 
    * the specified timeout has elapsed since the last time it was invoked.
    * @param {Function} callback - The function to debounce.
    * @param {number} [timeout=300] - The delay in milliseconds.
    * @returns {Function} The debounced function.
    */
   $.debounce = function(callback, timeout = 300) {
      let timer;
      
      return function(...args) {
         clearTimeout(timer);
         timer = setTimeout(() => callback.apply(this, args), timeout);
      };
   };

   /**
    * Converts an array to a Map with optional key mapping.
    * @param {Array} arr - The array to convert.
    * @param {*} [keyBy] - Optional key to use for mapping.
    * @returns {Map|Array} A Map if conversion is successful, otherwise the original array.
    */
   $.mapify = function (arr, keyBy) {
      if (!Array.isArray(arr) || arr.length === 0) {
         return arr;
      }

      if(keyBy){
         return new Map(arr.map((item) => [keyBy, item]));
      }

      return new Map(arr.map((item) => [item, item]));
   }

   /**
    * Restricts input to numeric characters only.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('#phone-input').numberOnly()
    */
   $.fn.numberOnly = function () {
      const NAMESPACE = 'numbersOnly';
      const INPUT_EVENTS = $.ns(
         NAMESPACE,
         'keydown',
         'input',
         'keyup',
         'paste'
      );

      return this.each(function () {
         const $el = $(this);

         $el.on(INPUT_EVENTS, function () {
            const hasNumbers = /^\d*$/.test(this.value);
            if (hasNumbers) {
               this.oldValue = this.value;
            } else if (this.hasOwnProperty("oldValue")) {
               this.value = this.oldValue;
            } else {
               this.value = "";
            }
         });
      });
   };

   /**
    * Initializes an HTML editor (Summernote) with customizable options.
    * @param {Object} [options={}] - Configuration options for the editor.
    * @param {number} [options.minHeight=300] - Minimum height of the editor.
    * @param {number} [options.maxHeight=800] - Maximum height of the editor.
    * @param {Array} [options.toolbar] - Toolbar configuration.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('#editor').editorHtml({ minHeight: 200 })
    */
   $.fn.editorHtml = function (options = {}) {
      const defaults = {
         minHeight: 300,
         maxHeight: 800,
         toolbar: [
         ['style', ['bold', 'italic', 'underline', 'clear']],
         ['font', ['strikethrough', 'superscript', 'subscript']],
         ['fontname', ['fontname']],
         ['fontsize', ['fontsize']],
         ['color', ['color']],
         ['para', ['ul', 'ol', 'paragraph']],
         ['insert', ['picture', 'link', 'video', 'table']],
         ['misc', ['codeview', 'fullscreen', 'undo', 'redo']]
         ]
      };

      const config = $.extend(true, {}, defaults, options);

      return this.each(function () {
         const $el = $(this);

         $el.summernote(config);
      });
   };

   /**
    * Toggles password visibility on mousedown/touchstart.
    * Requires a data-toggle attribute pointing to the input element.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('.toggle-password').passwordToggler()
    * // HTML: <i class="toggle-password fa fa-eye" data-toggle="#password-input"></i>
    */
   $.fn.passwordToggler = function () {
      const NAMESPACE = 'passwordToggler';
      const MOUSE_DOWN_EVENTS = $.ns(
         NAMESPACE,
         'mousedown',
         'touchstart'
      );
      const MOUSE_UP_EVENTS = $.ns(
         NAMESPACE,
         'mouseup',
         'touchend'
      );

      return this.each(function () {
         const $toggle = $(this);
         const $inputTarget = $($toggle.data('toggle'));

         $toggle.on(MOUSE_DOWN_EVENTS, function () {
            $inputTarget.attr('type', 'text');
            $toggle.removeClass('fa-eye').addClass('fa-eye-slash');
         });

         $toggle.on(MOUSE_UP_EVENTS, function () {
            $inputTarget.attr('type', 'password');
            $toggle.removeClass('fa-eye-slash').addClass('fa-eye');
         });
      });
   };

   /**
    * Makes an element draggable via a handle with class 'draggable-move'.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('.editor').dragElement()
    * // HTML: <div class="editor"><div class="draggable-move"></div>...</div>
    */
   $.fn.dragElement = function () {
      const SELECTOR = '.draggable-move';
      const NAMESPACE = 'drageElement';
      const MOVE_EVENTS = $.ns(
         NAMESPACE,
         'mousemove',
         'touchmove'
      );
      const UP_EVENTS = $.ns(
         NAMESPACE,
         'mouseup',
         'touchend',
         'touchcancel'
      );
      const DOWN_EVENTS = $.ns(
         NAMESPACE,
         'mousedown',
         'touchstart',
         'touchcancel'
      );

      return this.each(function () {
         const $target = $(this);

         if ($target.css('position') === 'static') {
            $target.css('position', 'absolute');
         }

         const $icon = $('<div>').addClass('icon-grip-horizontal');
         $icon.css({
            'margin': '20px',
            'font-size': '16px'
         });
         $(SELECTOR, $target).append($icon);

         let dragging = false;
         let startX = 0, startY = 0;
         let origLeft = 0, origTop = 0;

         $target.on(DOWN_EVENTS, SELECTOR, function(e){
            e.preventDefault();
            const ev = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
            dragging = true;
            startX = ev.clientX;
            startY = ev.clientY;
            origLeft = parseInt($target.css('left'), 10);
            origTop  = parseInt($target.css('top'), 10);
            if (isNaN(origLeft)) origLeft = $target.position().left;
            if (isNaN(origTop))  origTop  = $target.position().top;

            $(document).on(MOVE_EVENTS, onMove);
            $(document).on(UP_EVENTS, stopMove);
         });

         function onMove(e){
            if (!dragging) return;
            const ev = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
            const dx = ev.clientX - startX;
            const dy = ev.clientY - startY;
            $target.css({
               left: (origLeft + dx) + 'px',
               top:  (origTop  + dy) + 'px'
            });
         }

         function stopMove(){
            dragging = false;
            $(document).off('.' + NAMESPACE);
         }
      })
   }

   /**
    * Enables Tab key for indentation in textareas.
    * Tab adds indentation, Shift+Tab removes it.
    * @param {Object} options - Configuration options.
    * @param {Function} [options.afterChange] - Callback executed after text change.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('textarea').tabIdent({ afterChange: (e) => console.log('Changed!') })
    */
   $.fn.tabIdent = function (options) {
      const NAMESPACE = 'tabIndet';
      const KEYDOWN_EVENTS = $.ns(
         NAMESPACE,
         'keydown'
      );
      
      $(this).on(KEYDOWN_EVENTS, function(e) {
         if (e.key !== 'Tab') return;
         e.preventDefault();

         const el = this;
         const value = el.value;
         const start = el.selectionStart;
         const end = el.selectionEnd;
         const isShift = e.shiftKey;

         const selStart = value.lastIndexOf('\n', start - 1) + 1;
         const selEnd = end;
         const lines = value.substring(selStart, selEnd).split('\n');

         let modified;

         if (isShift) {
            modified = lines.map(line => line.replace(/^(\t| {1,4})/, '')).join('\n');
         } else {
            modified = lines.map(line => '\t' + line).join('\n');
         }

         const newValue = value.substring(0, selStart) + modified + value.substring(selEnd);
         el.value = newValue;

         el.selectionStart = selStart;
         el.selectionEnd = selStart + modified.length;

         if (options.afterChange !== undefined) {
            options.afterChange(e);
         }
      });

      return this;
   }

   /**
    * Creates a syntax-highlighted mirror editor for CSS.
    * @param {Object} options - Configuration options.
    * @param {Array} [options.highlight] - Array of token types to highlight.
    * @param {string} [options.theme='stardust'] - Theme name (stardust, vscode, light, night, matrix).
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('textarea').mirrorEditor({ theme: 'vscode', highlight: ['comments', 'classes'] })
    */
   $.fn.mirrorEditor = function (options) {
      const NAMESPACE = 'mirrorEditor';
      const INPUT_EVENTS = $.ns(
         NAMESPACE,
         'input'
      );
      const SCROLL_EVENTS = $.ns(
         NAMESPACE,
         'scroll'
      );
      const THEMES = {
         STARDUST: 'stardust',
         VSCODE: 'vscode',
         LIGHT: 'light',
         NIGHT: 'night',
         MATRIX: 'matrix'
      }
      const HIGHLIGHTS = {
         COMMENTS: 'comments',
         ID: 'id',
         CLASSES: 'classes',
         SELECTORS: 'selectors',
         ATTRIBUTES: 'attributes',
         VARIABLES: 'vars'
      }
      const DEFAULTS = {
         highlight: Object.values(HIGHLIGHTS),
         theme: THEMES.STARDUST,
      }

      const settings = $.extend(true, {}, options, DEFAULTS);
      
      /**
       * Escapes HTML special characters.
       * @private
       * @param {string} str - String to escape.
       * @returns {string} Escaped string.
       */
      function escapeHtml (str) {
         return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      }

      /**
       * Applies syntax highlighting to CSS text.
       * @private
       * @param {string} text - CSS text to highlight.
       * @returns {string} HTML with syntax highlighting spans.
       */
      function highlightCSS (text) {
         let escaped = escapeHtml(text);
         const highlightMap = $.mapify(settings.highlight);
         
         if(highlightMap.has(HIGHLIGHTS.COMMENTS)){
            escaped = escaped.replace(
               /(\/\*[\s\S]*?\*\/)/g,
               '<span class="token comment">$1</span>'
            );
         }

         if(highlightMap.has(HIGHLIGHTS.ID)){
            escaped = escaped.replace(/(#[a-zA-Z0-9_-]+)/g, '<span class="token id">$1</span>');
         }

         if(highlightMap.has(HIGHLIGHTS.CLASSES)){
            escaped = escaped.replace(/(\.[a-zA-Z0-9_-]+)/g, '<span class="token class">$1</span>');
         }

         if(highlightMap.has(HIGHLIGHTS.ATTRIBUTES)){
            escaped = escaped.replace(/(\[[^\]]+\])/g, '<span class="token attr">$1</span>');
         }

         if(highlightMap.has(HIGHLIGHTS.SELECTORS)){
            escaped = escaped.replace(
               /\b([a-zA-Z][a-zA-Z0-9]*)\b(?=\s*[{,])/g,
               '<span class="token selector">$1</span>'
            );
         }

         if(highlightMap.has(HIGHLIGHTS.VARIABLES)){
            escaped = escaped.replace(
               /(var)(\(--[a-zA-Z0-9_-]+\))/g,
               '<span class="token variable-func">$1</span><span class="token variable">$2</span>'
            );

            escaped = escaped.replace(
               /(--[a-zA-Z0-9_-]+)/g,
               '<span class="token variable">$1</span>'
            );
         }

         return escaped;
      }

      /**
       * Initializes the mirror editor for a textarea.
       * @private
       * @param {jQuery} $textarea - The textarea element.
       */
      function initMirrorEditor($textarea){
         const $theme = $('.mirror-theme').addClass('theme-' + settings.theme);
         const $highlight = $('<pre class="highlight-layer" aria-hidden="true"></pre>');

         $textarea.before($highlight);

         const syncScroll = () => {
            $highlight.scrollTop($textarea.scrollTop());
            $highlight.scrollLeft($textarea.scrollLeft());
         };

         const syncHighlight = () => {
            const text = $textarea.val();
            $highlight.html(highlightCSS(text) + '\n');
         };

         $textarea
            .on(INPUT_EVENTS, syncHighlight)
            .on(SCROLL_EVENTS, syncScroll)
            .trigger(INPUT_EVENTS);
      }

      return this.each(function(){
         initMirrorEditor($(this));
      });
   }

   /**
    * Creates a live CSS editor that applies styles in real-time.
    * Includes drag functionality, syntax highlighting, and validation.
    * @param {Object} [options={}] - Configuration options.
    * @returns {jQuery} The jQuery collection for chaining.
    * @example
    * $('.css-editor').cssEditor()
    */
   $.fn.cssEditor = function (options = {}) {
      const NAMESPACE = 'cssEditor';
      const EVENTS = {
         INPUT: $.ns(NAMESPACE, 'input'),
         CLICK: $.ns(NAMESPACE, 'click'),
      };
      const STYLE_SELECTOR = 'styles-css';

      /**
       * Validates CSS text using csstree validator.
       * @private
       * @param {string} cssText - CSS text to validate.
       * @returns {boolean} True if valid, false otherwise.
       */
      function validateCss(cssText) {
         try {
            const errors = csstreeValidator.validate(cssText);
            
            if(errors.length){
               console.error('❌ Error en CSS:', errors[0].formattedMessage);
               return false;
            }
            
            return true;
         } catch (error) {
            console.error('❌ Error en CSS:', error.message);
            return false;
         }
      }

      /**
       * Initializes the CSS editor with all functionality.
       * @private
       * @param {jQuery} $editor - The editor container element.
       */
      function initCssEditor($editor) {
         const ELEMENTS = {
            STYLE: $('#' + STYLE_SELECTOR),
            TEXTAREA: $('[data-textarea-css]', $editor),
            CLEAR: $('[data-clear-css]', $editor),
            COPY: $('[data-copy-css]', $editor),
            DRAGGABLE: $('[data-draggable-element-css]', $editor),
         }
         
         const PLUGINS = {
            DRAGGABLE: ELEMENTS.DRAGGABLE,
            TAB_IDENT: ELEMENTS.TEXTAREA,
            MIRROR_EDITOR: ELEMENTS.TEXTAREA,
         };

         initPlugins(PLUGINS);
         initStyle(ELEMENTS);
         initEvents(EVENTS, ELEMENTS);
      }

      /**
       * Initializes all required plugins for the editor.
       * @private
       * @param {Object} plugins - Object containing plugin elements.
       */
      function initPlugins(plugins){
         if(plugins.DRAGGABLE.length){
            plugins.DRAGGABLE.dragElement();
         }
         if(plugins.TAB_IDENT.length){
            plugins.TAB_IDENT.tabIdent(
               {
                  afterChange: function(e){         
                     $(e.target).trigger('input');
                  }
               }
            );
         }
         if(plugins.MIRROR_EDITOR.length){
            plugins.MIRROR_EDITOR.mirrorEditor();
         }
      }

      /**
       * Creates or retrieves the style element for live CSS.
       * @private
       * @param {Object} elements - Object containing editor elements.
       */
      function initStyle(elements){
         if(!elements.STYLE.length){
            const style = $('<style type="text/css" id="' + STYLE_SELECTOR + '">');
            $('head').append(style);

            elements.STYLE = style;
         }
      }

      /**
       * Binds all event handlers for the editor.
       * @private
       * @param {Object} events - Object containing event names.
       * @param {Object} elements - Object containing editor elements.
       */
      function initEvents(events, elements){
         elements.TEXTAREA.on(events.INPUT, $.debounce(function (event) {
            event.preventDefault();
            const cssCode = $(this).val();

            if (validateCss(cssCode)) {
               elements.STYLE.text(cssCode);
            }

         }, 500));

         elements.CLEAR.on(events.CLICK, function(event){
            event.preventDefault();

            elements.TEXTAREA.val('').trigger('input');
         });

         elements.COPY.on(events.CLICK, function(event){
            event.preventDefault();
            const value = elements.TEXTAREA.val();
            elements.COPY.tooltip({
               trigger: 'manual'
            });
            elements.COPY.tooltip('show');
            setTimeout(function(){
               elements.COPY.tooltip('hide');
            }, 1500);
            navigator.clipboard.writeText(value);
         });
      }

      return this.each(function () {
         initCssEditor($(this));
      });
   };
})(jQuery);