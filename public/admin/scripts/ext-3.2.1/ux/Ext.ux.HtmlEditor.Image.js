Ext.ns('Ext.ux.HtmlEditor.ButtonImage');

Ext.ux.HtmlEditor.ButtonImage = Ext.extend(Ext.util.Observable, {

    init: function(cmp){
		var bnt;
		var galley;
		
		this.gallery = cmp.gallery;
        this.cmp = cmp;
        this.cmp.on('render', this.onRender, this);

		var css = '.x-edit-image {background: url(images/icons/small/galeria.png) 0 0 no-repeat !important;}';
		Ext.util.CSS.createStyleSheet(css, 'editor-css');
    },

    onRender: function(){
    	this.cmp.getToolbar().addButton([new Ext.Toolbar.Separator()]);
		
        var cmp = this.cmp;
        this.btn = this.cmp.getToolbar().addButton({
            iconCls: 'x-edit-image',
            handler: this.show,
            scope : this,
            tooltip: {title: 'Inserir imagem'},
            overflowText: 'Inserir imagem'
        });
		this.btn.disable();
    },
	
	show : function() {
		App.Application.loadModule({
			name: 'App.Sistema.Galeria',
			url: String.format('{0}/sistema_galeria', QoDesk.App.desktop.baseUrl),
			callback: function() {
				App.Sistema.Galeria.show({
					sender: this.btn.getEl(),
					gallery: this.gallery,
					callback: function(arquivo) {
						this.cmp.append('<img src="'+arquivo.gal_arq_base+'" />');   	
					},
					scope: this
				});
			},
			scope: this
		});
	}
});

String.prototype.ellipse = function(maxLength){
    if(this.length > maxLength){
        return this.substr(0, maxLength-3) + '...';
    }
    return this;
};