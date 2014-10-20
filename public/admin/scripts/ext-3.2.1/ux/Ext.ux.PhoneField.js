Ext.ux.PhoneField = function(config){
		var defConfig = {
			maxLength: 14,
			minLength: 13
		};
		Ext.applyIf(config,defConfig);
    Ext.ux.PhoneField.superclass.constructor.call(this, config);
};

Ext.extend(Ext.ux.PhoneField, Ext.form.TextField,{
  
	initEvents : function() {
        Ext.ux.PhoneField.superclass.initEvents.call(this);
        this.el.on("focus", this.setMask,this); 
		this.el.on("keyup", this.txtBoxFormat,this); 
		this.el.on("blur", this.unsetMask,this); 
    },
    
    setMask: function() {
    	strField = this.el.dom;
    	var regex = /^\((\d{2})\)+(\d{4}|\d{5})+-(\d{4})$/;
		if(!regex.test(strField.value) || strField.value == '') strField.value = '(__)____-_____';	
		strField.select();	
    },
    
    unsetMask: function() {
    	strField = this.el.dom;
    	var regex = /^\((\d{2})\)+(\d{4}|\d{5})+-(\d{4})$/;
		if(!regex.test(strField.value)) strField.value = '';	
    },

    txtBoxFormat: function(evtKeyPress) {
    	
    	strField = this.el.dom;
    	var temp = strField.value;
    	
    	if(!temp) this.setMask();
    	else {
    	
	    	this.maxLength = ( temp.length <= 13 ) ? 13 : 14;
	    	
	    	temp = temp.replace(/\D/g,"");
	    	temp = temp.replace(/^(\d\d)(\d)/g,"($1)$2");
	    	
	    	if(temp.length < 13) {
	    		temp=temp.replace(/(\d{4})(\d)/,"$1-$2");
	    	} else {
	    		temp=temp.replace(/(\d{5})(\d)/,"$1-$2");
	    	}
	    	strField.value = temp;
    	}
    }
});

Ext.ComponentMgr.registerType('phonefield', Ext.ux.PhoneField);