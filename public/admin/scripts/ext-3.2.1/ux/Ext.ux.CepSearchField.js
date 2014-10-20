
Ext.namespace('Ext.ux');

Ext.ux.CepSearchField = Ext.extend(Ext.form.TwinTriggerField, {
	initComponent: function() {
		Ext.ux.CepSearchField.superclass.initComponent.call(this);
		this.addEvents(
			'search',
			'clear'
		);

		this.on('specialkey', function(f, e) {
			if(e.getKey() == e.ENTER){
				this.onTrigger2Click();
			}
		}, this);
	},

	trigger1Class:'x-form-clear-trigger',
	trigger2Class:'x-form-search-trigger',
	hideTrigger1:true,
	hasSearch : false,

	urlSearch: null,

	fields: {
		uf: null,
		cidade: null,
		bairro: null,
		endereco: null
	},

	onTrigger1Click: function() {
		if (this.hasSearch) {
			this.el.dom.value = '';
			Ext.getCmp(this.fields.uf).setValue('');
			Ext.getCmp(this.fields.cidade).setValue('');
			Ext.getCmp(this.fields.bairro).setValue('');
			Ext.getCmp(this.fields.endereco).setValue('');

			this.triggers[0].hide();
			this.hasSearch = false;
			this.el.removeClass('x-cepsearchfield-loading');
			this.fireEvent('clear', this);
		}
	},

	onTrigger2Click: function() {
		var v = this.getRawValue();
		if(v.length < 1) {
			this.onTrigger1Click();
			return;
		}
		this.el.addClass('x-cepsearchfield-loading');
		this.el.dom.value = '     Consultando...';
		Ext.Ajax.request({
			url: this.urlSearch,
			params: {cep: v},
			method: 'post',
			callback: function(options, success, response) {
				this.el.removeClass('x-cepsearchfield-loading');
				this.el.dom.value = v;
				if (success == true) {
					var resp = Ext.util.JSON.decode(response.responseText);
					Ext.getCmp(this.fields.uf).setValue(resp.estado);
					Ext.getCmp(this.fields.cidade).setValue(resp.cidade);
					Ext.getCmp(this.fields.bairro).setValue(resp.bairro);
					Ext.getCmp(this.fields.endereco).setValue(resp.endereco);
					this.validate();
					this.fireEvent('search', this, resp);
				} else {
					Ext.Msg.show({
						title: 'Busca de Endereço',
						icon: Ext.Msg.ERROR,
						msg: 'Não foi possível contactar o endereço remoto de pesquisa.<br />Por favor tente novamente mais tarde.',
						buttons: Ext.Msg.OK
					});
				}
			},
			scope:this
		});
		this.hasSearch = true;
		this.triggers[0].show();
	}
});

Ext.ComponentMgr.registerType('cepfield', Ext.ux.CepSearchField);