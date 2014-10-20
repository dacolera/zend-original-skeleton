(function() {
	tinymce.create('tinymce.plugins.CodeMirror', {
		
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			this.CMURL = url;
			//load editarea script
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', {src:url + '/jscript/codemirror/js/codemirror.js'});
			
			//load CSS
			tinymce.DOM.loadCSS(url + '/css/codemirror.css');
			
			// Register commands
			ed.addCommand('mceCodeMirror', t._editArea, t);
			
			// Register buttons
			ed.addButton('codemirror', {title : 'Advaced source editor', cmd : 'mceCodeMirror'});

			ed.onNodeChange.add(t._nodeChange, t);
		},

		getInfo : function() {
			return {
				longname : 'CodeMirror integration for TinyMCE',
				author : 'Alaa-eddine KADDOURI',
				authorurl : 'http://www.eurekaa.org',
				infourl : 'http://code.google.com/p/eurekaa/',
				version : '0.2 Beta'
			};
		},

		_nodeChange : function(ed, cm, n) {
			var ed = this.editor;
			//not used for the moment
		},

		_editArea : function() {
			var ed = this.editor, formObj, os, i, elementId;
			this._showEditArea();
		},
	
		_showEditArea : function()
		{					
			var n, t = this, ed = t.editor, s = t.settings, r, mf, me, td;
				baseurl = this.CMURL + '/jscript/codemirror/';
				areaId = ed.getElement().id;				
				mw = ed.getContainer().firstChild.style.width;
				mh = ed.getContainer().firstChild.style.height;
				ed.hide();						
				
				  t.CMEditor = CodeMirror.fromTextArea(areaId, {
					height: mh,
					width: mw,
					parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],
					stylesheet: [baseurl+"css/xmlcolors.css", baseurl+"css/jscolors.css", baseurl+"css/csscolors.css"],
					path: baseurl + "js/"
				  });
				
				fc_click = function() {
					ed.show();
					ed.setContent(t.CMEditor.getCode());
					t.CMEditor.frame.parentNode.removeChild(t.CMEditor.toolBarDiv);
					t.CMEditor.frame.parentNode.removeChild(t.CMEditor.frame);
					t.CMEditor = null;
				};
				
				t.CMEditor.frame.id = 'frame_'+areaId;
				t.CMEditor.frame.style.width = mw;
				t.CMEditor.frame.style.height = mh;
				t.CMEditor.frame.style.border = '1px solid black';
				t.CMEditor.frame.className = 'CodeMirrorFrame';
				
				t.CMEditor.toolBarDiv = document.createElement("div");
				t.CMEditor.toolBarDiv.id = 'div_'+areaId
				t.CMEditor.toolBarDiv.style.width = mw;
				t.CMEditor.toolBarDiv.className = 'CodeMirrorToolBar';				
				t.CMEditor.frame.parentNode.insertBefore(t.CMEditor.toolBarDiv, t.CMEditor.frame);
				
				btn = tinymce.DOM.add(t.CMEditor.toolBarDiv, 'input', {type:'button', value:'Close', 'class':'CMBtn', id:'Btn_'+areaId});
				btn.onclick = fc_click;
				
		}		

	});

	// Register plugin
	tinymce.PluginManager.add('codemirror', tinymce.plugins.CodeMirror);
})();