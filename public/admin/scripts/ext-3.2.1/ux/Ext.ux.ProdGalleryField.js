Ext.namespace('Ext.ux');

Ext.ux.ProdGalleyField = Ext.extend(Ext.form.Field, {
	gallery: null,
	parent: null,
	widthThumb: 120,
	heightThumb: 90,
	widthLabel: 120,
	heightLabel: 60,
	thumbVisible: true,
	labelVisible: true,
	hiddenKit: false,
	
	/**
	 * Tipo 1: Exibe Todos os Produtos e SKUs
	 * Tipo 2: Exibe somente os produtos
	 * Tipo 3: Exibe somente os SKUs
	 * Tipo 4: Exibe produtos e SKUs escondendo o SKU quando o produto tiver somente uma variação
	 * 
	 * @type Number
	 */ 
	listType: 1, 
	
	disabledButton: false,
	btnAdd: null,

	defaultButtons: {
		add: {
			text: 'Selecionar',
			tooltip: {title: 'Selecionar arquivo', text: 'Seleciona produto da galeria'},
			iconCls: 'icon-insert'
		},
		remove: {
			text: 'Remover',
			tooltip: {title: 'Remover arquivo', text: 'Remove o produto selecionado selecionado'},
			iconCls: 'icon-delete'
		}
	},

	buttons: {
		add: {},
		remove: {}
	},
	
	//private
	thumbCls: 'x-form-field-gallery-thumb',

	//private
	defaultAutoCreate : {tag: 'input', type: 'hidden'},

	initComponent: function() {
		Ext.ux.ProdGalleyField.superclass.initComponent.call(this);

		this.addEvents(
			'selectImagem'
		);
	},

	onRender: function(ct, position) {
		
		Ext.ux.ProdGalleyField.superclass.onRender.call(this, ct, position);

		this.wrap = this.el.wrap();

		if(this.thumbVisible) {
			this.thumbBorder = this.wrap.createChild({
				tag: 'div',
				cls: this.thumbCls,
				style: String.format('margin:0 5px 5px 0; width:{0}px; height:{1}px; float:left;', this.widthThumb, this.heightThumb)
			});
		}
		
		if(this.labelVisible){
			this.labelInfo = this.wrap.createChild({
				tag: 'div',
				style: String.format('text-align:left; margin:0 0 5px 0; width:{1}; height:{0}px; float:none;', this.heightLabel, (this.wrap.getWidth() - this.widthThumb - 20))
			});
		}

		var margin = (this.labelVisible || this.thumbVisible) ? 'margin:5px 0 10px 5px;' : 'margin:0;';
		
		this.panelButtons = this.wrap.createChild({
			tag: 'div',
			style: margin
		});

		Ext.apply(this.defaultButtons.add, this.buttons.add);
		Ext.apply(this.defaultButtons.add, {
			renderTo: this.panelButtons,
			style: 'float: left; margin-right: 5px;',
			listeners: {
				click: function(button, event) {
					App.Application.loadModule({
						name: 'App.Produtos.Galeria',
						url: App.Application.getBaseUrl()+'/produtos_galeria',
						callback: function() {
							App.Produtos.Galeria.show({
								sender: button.getEl(),
								singleSelection: true,
								listType: this.listType,
								callback: function(selected) {
									this.setValue(selected[0].data);
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
				listeners: {
					click: function(button, event) {
						this.reset();
					},
					scope: this
				}
			});
			this.btnRemove = new Ext.Button(this.defaultButtons.remove);
		}
	},

	onDestroy: function() {
		Ext.destroy(this.btnAdd);
		Ext.destroy(this.btnRemove);
		Ext.destroy(this.panelButtons);
		Ext.destroy(this.thumb);
		Ext.destroy(this.thumbBorder);
		Ext.destroy(this.wrap);
		Ext.ux.ProdGalleyField.superclass.onDestroy.call(this);
	},
	
	setDisabled: function(bool) {
		Ext.apply(this.btnAdd, {disabled: true});
		Ext.apply(this.btnRemove, {disabled: true});
	},

	setValue: function(value) {

		if (this.thumbVisible) {
			if (this.thumb) Ext.destroy(this.thumb);

			string = '';
			switch(value.prod_tipo){
				case 'P': string = 'resizeprodutos'; 	break;
				case 'S': string = 'resizesku'; 		break;
			}

			this.thumb = this.thumbBorder.createChild({
				tag: 'image',
				src: String.format('{0}/produtos_galeria/index/load/{1}?id={2}&width={3}&height={4}', App.Application.getBaseUrl(), string, value.prod_idProduto, this.widthThumb, this.heightThumb)
			});
		}
		
		this.btnAdd.disable = this.disabledButton;

		if (this.labelVisible) {
			this.labelInfo.update(String.format('<b>Nome:</b> {0}<br /><b>Preço:</b> {1}', value.prod_nome, Ext.util.Format.brMoney(value.prod_preco)));
		}
		
		if (this.allowBlank == true) {
			this.btnRemove.setDisabled(!value);
		}

		return Ext.ux.ProdGalleyField.superclass.setValue.call(this, Ext.util.JSON.encode(value));
	},

	getValue: function() {
		return Ext.ux.ProdGalleyField.superclass.getValue.call(this);
	},

	validateValue: function(value) {
		if (this.allowBlank == false && !value) {
			if (this.rendered && this.thumbVisible) {
				this.thumbBorder.addClass(this.invalidClass);
				this.thumbBorder.removeClass(this.thumbCls);
			}
			return false;
		} else {
			if (this.rendered && this.thumbVisible) {
				this.thumbBorder.addClass(this.thumbCls);
				this.thumbBorder.removeClass(this.invalidClass);
			}
		}
		return true;
	}
});

Ext.ComponentMgr.registerType('prodgalleryfield', Ext.ux.ProdGalleyField);