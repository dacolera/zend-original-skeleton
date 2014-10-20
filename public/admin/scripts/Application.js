Ext.namespace('App');

App.Application = function() {
	var config = {};

	var msgCt;
	var tplMsgCt;

	var viewport;
	var desktop;
	var desktopMenu;
	var contextTab;

	function loadScript(id, file, callback, scope) {
        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('id', id);

        if (document.all) {
            script.onreadystatechange = function () {
                if (script.readyState == 'loaded' || script.readyState == 'complete') {
                    script.onreadystatechange = null;
                    if (callback) callback.call(scope);
                }
            };
        } else {
            script.onload = function () {
                if (callback) callback.call(scope, id);
            };
        }

        script.setAttribute('src', file);
        document.getElementsByTagName('head')[0].appendChild(script);
    };
    
	function showError(erro) {
		var wndError = new Ext.Window({
			title: 'Erro na aplicação',
			iconCls: 'icon-cancel-small',
			modal: true,
			constrain: true,
			width: 500,
			height: 400,
			autoScroll: true,
			html: erro,
			buttons: [{
				text: 'Recarregar',
				iconCls: 'icon-refresh',
				handler: function(button, event) {
					document.location.href = document.location.href;
				}
			}, {
				text: 'Fechar',
				iconCls: 'icon-cancel-small',
				handler: function(button, event) {
					wndError.close();
				},
				scope: this
			}]
		}).show();
	}

	return {
		start: function(cfg) {
			Ext.QuickTips.init();
			Ext.BLANK_IMAGE_URL = 'scripts/ext-3.2.1/resources/images/default/s.gif';
			Ext.chart.Chart.CHART_URL = 'scripts/ext-3.2.1/resources/charts.swf';
			Ext.Ajax.defaultHeaders = {
                'Charset': 'ISO-8859-1',
                'Pragma': 'no-cache',
                'Cache-Control': 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Expires': '0'
            };
			
			Ext.Ajax.on('requestcomplete', function(conn, response, options) {
				var regex = /^(\{|\[).*(\}|\])$/;
				if (regex.test(response.responseText) == false) {
					showError(response.responseText);
				}
			}, this);

			//mensagens de alerta
			msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
	        msgCt.setStyle('position', 'absolute');
	        msgCt.setStyle('z-index', 9999);
	        //msgCt.setWidth(500);
	    	tplMsgCt = new Ext.XTemplate(
	    		'<div class="app-msg">',
	    			'<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
	    			'<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">',
	    				'<tpl if="title"><h1>{title}</h1></tpl>',
	    				'<div>{msg}</div>',
	    			'</div></div></div>',
	    			'<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
    			'</div>'
    		);

			config = cfg;

			desktop = new Ext.TabPanel({
				enableTabScroll: true,
				layoutOnTabChange: true,
				forceLayout: true,
				border: false,
				activeTab: 0,
				items: [{
					title: 'Home',
					html: '&nbsp;',
					autoScroll: true
				}],
				listeners: {
					contextmenu: function(panel, tab, event) {
						contextTab = tab;

						if (!desktopMenu) {
							desktopMenu = new Ext.menu.Menu({            
								items: [{
									text: 'Fechar guia',
									iconCls: 'icon-close-tab',
									handler: function(button, event) {
										if (contextTab.closable) {
											desktop.remove(contextTab, true);
										}
									},
									scope: this
								}, {
									text: 'Fechar todas as guias',
									iconCls: 'icon-close-alltab',
									handler: function(button, event) {
										desktop.items.each(function(tab) {
											if (tab.closable && tab) {
												desktop.remove(tab, true);
					                        }
										});
					                },
					                scope: this
								}, {
									text: 'Fechar todas as outras guias',
									iconCls: 'icon-close-alltab',
									handler: function(button, event) {
										desktop.items.each(function(tab) {
											if (tab.closable && tab != contextTab) {
												desktop.remove(tab, true);
					                        }
										});
					                },
					                scope: this
								}]
							});
						}

						desktopMenu.items.get(0).setDisabled(!tab.closable);
						event.stopEvent();
						desktopMenu.showAt(event.getPoint());
					},
					scope: this
				}
			});

			viewport = new Ext.Viewport({
				layout: 'fit',
				items: [{
					layout: 'fit',
					border: false,
					tbar: [config.menu],
					items: [{
						xtype: 'panel',
						border: false,
						layout: 'fit',
						items: desktop
					}],
					bbar: new Ext.ux.StatusBar({
						id: 'app-statusbar',
						defaultText: 'Módulo Administrativo',
						items: [
							'-',
							String.format('<table><tr><td><img src="{0}/images/icons/small/user-logged.png" border="0" /></td><td class="usr-logado">&nbsp;{1}&nbsp;</td></tr></table>', config.baseUrl, config.usuario.nome),
							'-',
							'<table><tr><td class="padrao"><a class="byvm2" href="http://www.vm2.com.br" target="_blank" title="Agência VM2">by VM2</a></td></tr></table>'
						]
					})
				}]
			});

			//manter logado
			var runner = new Ext.util.TaskRunner();
			runner.start({
				interval: 300000,
				run: function() {
					Ext.Ajax.request({
						url: String.format('{0}/index/keep-alive', config.baseUrl),
						method: 'GET'
					});
				}
			});
		},
		
		loadModule: function (config) {
            Ext.applyIf(config, {
                callback: Ext.emptyFn,
                scope: null
            });

            var run = function (module, config) {
                module.init.call(module);
                if (config.callback) {
                    config.callback.call(config.scope);
                }
            };

            loadScript(config.name, config.url, function () {
               // Ext.getCmp('app-statusbar').clearStatus({ useDefaults: true });
                try {
                    module = eval(config.name);
                    run(module, config);
                } catch (e) {
                    showError(e.message);
                }
            }, this);
        },
		
		addTabPanel: function(config) {
			Ext.applyIf(config, {
				closable: true,
				border: false,
				layout: 'fit'
			});
			var oItem = desktop.find('title', config.title)[0];
			if (!oItem) {
				var oItem = desktop.add(config);
			}
			//desktop.setActiveTab(oItem);
			if (oItem.title != desktop.getActiveTab().title) {
				desktop.setActiveTab(oItem);
			}
		},
		
		removeActiveTab: function () {
            return desktop.remove(desktop.getActiveTab());
        },
        
        getActiveTab: function () {
            return desktop.getActiveTab();
        },

		getUsuario: function() {
			return config.usuario;
		},

		getBaseUrl: function() {
			return config.baseUrl;
		},

		Message: function(config) {
			Ext.applyIf(config, {
				target: document.body,
				position: 'tl-bl',
				animation: 'tl',
				delay: 4
			});

			tplMsgCt.overwrite(msgCt, {title: config.title, msg: config.msg});
			msgCt.anchorTo(config.target, config.position);
			msgCt.slideIn(config.animation).pause(config.delay).slideOut(config.animation, {remove:false});
		}
	};
}();
