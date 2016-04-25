(function($)
{
	$.Redactor.prototype.filemanager = function()
	{
		return {
			langs: {
				en: {
					"upload": "Upload",
					"choose": "Choose"
				}
			},
			init: function()
			{
				if (!this.opts.fileManagerJson)
				{
					return;
				}

				this.modal.addCallback('file', this.filemanager.load);
			},
			load: function()
			{
				var $box = $('<div  style="overflow: auto; height: 300px; display: none;" class="redactor-modal-tab" data-title="Choose">').hide();
				this.modal.getModal().append($box);

				$.ajax({
				  dataType: "json",
				  cache: false,
				  url: this.opts.fileManagerJson,
				  success: $.proxy(function(data)
					{
						var ul = $('<ul id="redactor-modal-list">');
						$.each(data, $.proxy(function(key, val)
						{
							var a = $('<a href="#" title="' + val.title + '" rel="' + val.link + '" class="redactor-file-manager-link">' + val.title + ' <span style="font-size: 11px; color: #888;">' + val.name + '</span> <span style="position: absolute; right: 10px; font-size: 11px; color: #888;">(' + val.size + ')</span></a>');
							var li = $('<li />');

							a.on('click', $.proxy(this.filemanager.insert, this));

							li.append(a);
							ul.append(li);

						}, this));

						$box.append(ul);


					}, this)
				});

			},
			insert: function(e)
			{
				e.preventDefault();

				var $target = $(e.target).closest('.redactor-file-manager-link');
				var link = document.createElement('a');
				link.href = $target.attr('rel');
				link.innerHTML = $target.attr('title');

				this.file.insert(link);
			}
		};
	};
})(jQuery);