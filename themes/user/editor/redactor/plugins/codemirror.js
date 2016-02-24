(function($)
{
	$.Redactor.prototype.codemirror = function()
	{
		return {
			init: function()
			{
				var button = this.button.addFirst('html', 'HTML');
				this.button.addCallback(button, this.codemirror.toggle);

				this.codemirror.$textarea = $('<textarea />');
				this.codemirror.$textarea.hide();

				if (this.opts.type === 'textarea')
				{
					this.core.box().append(this.codemirror.$textarea);
				}
				else
				{
					this.core.box().after(this.codemirror.$textarea);
				}

				this.core.element().on('destroy.callback.redactor', $.proxy(function()
				{
					this.codemirror.$textarea.remove();

				}, this));

				var settings = (typeof this.opts.codemirror !== 'undefined') ? this.opts.codemirror : { lineNumbers: true, mode: "htmlmixed"};
				this.codemirror.editor = CodeMirror.fromTextArea(this.codemirror.$textarea[0], settings);

				this.codemirror.$textarea.next('.CodeMirror').hide();

			},
			toggle: function()
			{
				return (this.codemirror.$textarea.hasClass('open')) ? this.codemirror.hide() : this.codemirror.show();
			},
			hide: function()
			{
				var code;
				this.codemirror.$textarea.removeClass('open').hide();
				this.codemirror.$textarea.next('.CodeMirror').hide().each(function(i, el)
				{
					code = el.CodeMirror.getValue();
				});

				code = this.paragraphize.load(code);
				this.code.start(code);
				this.button.enableAll();
				this.core.editor().show().focus();
				this.code.sync();

			},
			show: function()
			{
				var height = this.core.editor().innerHeight();
				var code = this.code.get();

				code = code.replace(/\n\n/g, "\n");

				this.core.editor().hide();
				this.button.disableAll('html');

				this.codemirror.$textarea.val(code).height(height).addClass('open');
				this.codemirror.$textarea.next('.CodeMirror').each(function(i, el)
				{
					$(el).show();
					el.CodeMirror.setValue(code);
					el.CodeMirror.setSize('100%', height);
					el.CodeMirror.refresh();

					el.CodeMirror.focus();
				});

			}
		};
	};
})(jQuery);