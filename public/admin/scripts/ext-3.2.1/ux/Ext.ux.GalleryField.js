Ext.namespace('Ext.ux');

Ext.ux.GalleryField = Ext.extend(Ext.form.Field, {
	gallery: null,
	parent: null,
	onlyImages: false,
	onlyVideos: false,
	videosExtensions: ['FLV', 'AVI'],
	imagesExtensions: ['GIF', 'JPG', 'PNG'],
	info: false,
	infoTemplate: new Ext.XTemplate(
		'<input type="text" value="{gal_arq_nome}" readonly="true" class="x-form-text x-form-field" style="width:100%" />'
	),
	
	defaultButtons: {
		add: {
			text: 'Selecionar',
			tooltip: {title: 'Selecionar arquivo', text: 'Seleciona um arquivo da galeria'},
			iconCls: 'icon-insert'
		},
		remove: {
			text: 'Remover',
			tooltip: {title: 'Remover arquivo', text: 'Remove o arquivo selecionado'},
			iconCls: 'icon-delete'
		}
	},

	buttons: {
		add: {},
		remove: {}
	},

	buttonAlign: 'bottom',

	//private
	thumbCls: 'x-form-field-gallery-thumb',

	//private
	defaultAutoCreate: {tag: 'input', type: 'hidden'},

	initComponent: function() {
		Ext.ux.GalleryField.superclass.initComponent.call(this);

		this.addEvents(
			'selectImagem'
		);
	},

	onRender: function(ct, position) {
		Ext.ux.GalleryField.superclass.onRender.call(this, ct, position);
		this.wrap = this.el.wrap();

		this.thumbBorder = this.wrap.createChild({
			tag: 'div',
			cls: this.thumbCls,
			style: String.format('float:left; width:{0}px; height:{1}px;', this.width, this.height)
		});

		if (this.info == true) {
			this.panelInfo = this.wrap.createChild({
				tag: 'div',
				style: String.format('float:left; margin-left:5px; height: {0}px;', this.height)
			});
			this.panelInfo.dom.innerHTML = this.infoTemplate.apply({});
		}

		this.panelButtons = this.wrap.createChild({
			tag: 'div',
			style: String.format('display:inline-block; {0}', (this.buttonAlign == 'right' ? 'float:right; display:inline-block;' : 'clear:both; margin-top:5px;'))
		});

		Ext.apply(this.defaultButtons.add, this.buttons.add);
		Ext.apply(this.defaultButtons.add, {
			renderTo: this.panelButtons,
			disabled: false,
			style: 'float: left;',
			listeners: {
				click: function(button, event) {
					App.Application.loadModule({
						name: 'App.Sistema.Galeria',
						url: String.format('{0}/sistema_galeria', App.Application.getBaseUrl()),
						callback: function() {
							App.Sistema.Galeria.show({
								sender: button.getEl(),
								gallery: this.gallery,
								singleSelect: true,
								callback: function(arquivo) {
									if (this.onlyImages || this.onlyVideos) {
										if (!this.validateExtension(arquivo.gal_arq_base.substr(arquivo.gal_arq_base.lastIndexOf('.') + 1, arquivo.gal_arq_base.length))) return false;
									}

									this.path = arquivo.gal_arq_base;
									this.setValue(this.info == true ? arquivo : arquivo.gal_arq_idArquivo);
									this.fireEvent('selectImagem', arquivo.gal_arq_idArquivo);
								},
								scope: this
							});
						},
						scope: this
					});
				},
				scope: this
			}
		});
		this.btnAdd = new Ext.Button(this.defaultButtons.add);

		if (this.allowBlank == true) {
			Ext.apply(this.defaultButtons.remove, this.buttons.remove);
			Ext.apply(this.defaultButtons.remove, {
				renderTo: this.panelButtons,
				disabled: true,
				style: 'float:left; margin-left:5px;',
				listeners: {
					click: function(button, event) {
						this.reset();
					},
					scope: this
				}
			});
			this.btnRemove = new Ext.Button(this.defaultButtons.remove);
		}

		this.panelButtons.applyStyles(String.format('width:{0}px', this.panelButtons.getWidth()));
		//console.log(this.fieldLabel +' - '+ this.panelButtons.getWidth());
	},

	onDestroy: function() {
		Ext.destroy(this.btnAdd);
		Ext.destroy(this.btnRemove);
		Ext.destroy(this.panelButtons);
		Ext.destroy(this.thumb);
		Ext.destroy(this.thumbBorder);
		Ext.destroy(this.wrap);
		Ext.ux.GalleryField.superclass.onDestroy.call(this);
	},

	setValue: function(value) {
		if (this.thumb) {
			Ext.destroy(this.thumb);
			if (this.info == true && !value) {
				this.panelInfo.dom.innerHTML = this.infoTemplate.apply({});
			}
		}
		if (this.rendered) {
			if (value) {
				this.thumb = this.thumbBorder.createChild({
					tag: 'image',
					src: String.format('{0}/utils/resize-galeria?imagem={1}&width={2}&height={3}', App.Application.getBaseUrl(), (this.info == true ? value.gal_arq_idArquivo : value), this.width, this.height)
				});

				if (this.info == true) {
					this.panelInfo.dom.innerHTML = this.infoTemplate.apply(value);
				}
			}
	
			if (this.allowBlank == true) {
				this.btnRemove.setDisabled(!value);
			}
		}
		
		return Ext.ux.GalleryField.superclass.setValue.call(this, (this.info == true ? value.gal_arq_idArquivo : value));
	},

	validateExtension: function(extension) {
		var accept = false;
		var tiposArquivos = '';
		if(this.onlyImages) {
			tiposArquivos = this.imagesExtensions.toString();
			for(i=0; i<this.imagesExtensions.length; i++){
				if(extension.toUpperCase() == this.imagesExtensions[i]) accept = true;
			}
		} else if(this.onlyVideos){
			tiposArquivos = this.videosExtensions.toString();
			for(i=0; i<this.videosExtensions.length; i++){
				if(extension.toUpperCase() == this.videosExtensions[i]) accept = true;
			}
		}
		
		
		if(!accept) {
			Ext.Msg.show({
				title: 'Galeria de Arquivos',
				msg: 'Tipo de arquivo não permitido.<br>Este campo só aceita arquivos do tipo <b>'+ tiposArquivos +'</b>.',
				icon: Ext.Msg.ERROR,
				buttons: Ext.Msg.OK
			});
			return false;
		}
		return true;
	},
	
	getValue: function() {
		return Ext.ux.GalleryField.superclass.getValue.call(this);
	},

	validateValue: function(value) {
		if (this.allowBlank == false && !value) {
			if (this.rendered) {
				this.thumbBorder.addClass(this.invalidClass);
				this.thumbBorder.removeClass(this.thumbCls);
			}
			return false;
		} else {
			if (this.rendered) {
				this.thumbBorder.addClass(this.thumbCls);
				this.thumbBorder.removeClass(this.invalidClass);
			}
		}
		return true;
	}
});

Ext.ComponentMgr.registerType('galleryfield', Ext.ux.GalleryField);