Ext.apply(Ext.data.Types, {
	LIST: {
		convert: function(value, data) {
			var retorno = null;
			Ext.each(this.options, function(item) {
				if (item[0] == value) {
					retorno = item[1];
				} else if (item[1] == value) {
					retorno = item[0];
				}
			}, this);
			return retorno || value;
		},
	
		//sortType: Ext.data.SortTypes.asList,
		sortType: function(value) {
			console.log(value, Ext.data.Types.LIST.convert(value) );
			//return this.convert(value);
		},
	
		type: 'list'
	}
});


if(Ext.form.VTypes){
	Ext.apply(Ext.form.VTypes, {
		cpf: function(v) {
			if (!/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(v)) return false;
			v = v.replaceAll('.', '').replaceAll('-', '');
			var digitos_iguais = 1;
			for (i=0; i<v.length-1; i++) {
				if (v.charAt(i) != v.charAt(i + 1)) {
					digitos_iguais = 0;
					break;
				}
			}
			if (!digitos_iguais) {
				var numeros = v.substring(0, 9);
				var digitos = v.substring(9);
				var soma = 0;

				for (var i=10; i>1; i--) soma += numeros.charAt(10 - i) * i;
				var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(0)) return false;
				numeros = v.substring(0, 10);
				soma = 0;
				for (var i=11; i>1; i--) soma += numeros.charAt(11 - i) * i;
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado == digitos.charAt(1)) return true;
			}
			return false;
		},
		cpfText: 'Informe um CPF v&aacute;lido no formato XXX.XXX.XXX-XX',

		cnpj: function(v) {
			if (!/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/.test(v)) return false;
			v = v.replaceAll('.', '').replaceAll('-', '').replaceAll('/', '');

			var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
			var digitos_iguais = 1;
			for (var i=0; i<v.length-1; i++) {
				if (v.charAt(i) != v.charAt(i + 1)) {
					digitos_iguais = 0;
					break;
				}
			}
			if (!digitos_iguais) {
				var tamanho = v.length - 2;
				var numeros = v.substring(0, tamanho);
				var digitos = v.substring(tamanho);
				var soma = 0;
				var pos = tamanho - 7;
				for (i=tamanho; i>=1; i--) {
					soma += numeros.charAt(tamanho - i) * pos--;
					if (pos < 2) pos = 9;
				}
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(0)) return false;
				tamanho = tamanho + 1;
				numeros = v.substring(0, tamanho);
				soma = 0;
				pos = tamanho - 7;
				for (i=tamanho; i>=1; i--) {
					soma += numeros.charAt(tamanho - i) * pos--;
					if (pos < 2) pos = 9;
				}
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado == digitos.charAt(1)) return true;
			}
			return false;
		},
		cnpjText: 'Informe um CNPJ v&aacute;lido no formato XX.XXX.XXX-XXXX/XX',
		
		telefone: function(v) {
			return /^\(\d{2}\)\d{4}-\d{4}$/.test(v);
		},
		telefoneText: 'Informe o n&uacute;mero no formato (XX) XXXX-XXXX',

		time: function(v) {
			return /^(([0-1][0-9])|([2][0-3])):([0-5][0-9])$/.test(v);
		},
		timeText: 'Informe a hora no formato HH:MM',

		login: function(v) {
			return /^([a-z](?:(?:(?:\w[\.\_]?)*)\w)+)([a-z0-9])$/.test(v);
		},
		loginText: 'N&atilde;o utilize caracteres especiais e letras mai&uacute;sculas',

		senha: function(v) {
			return /^(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{5,15})$/.test(v);
		},
		senhaText: 'A senha deve ter entre 5 e 15 d&iacute;gitos, n&atilde;o conter caracteres especiais (*,.^_@+-) e conter pelo menos 1 n&uacute;mero e 1 letra',

		diretorio: function(v) {
			return /^(?:(?:(?:\w[\.\_]?)*)\w)$/.test(v);
		},
		diretorioText: 'N&atilde;o utilize caracteres especiais'
	});
}


String.prototype.replaceAll = function(de, para) {
    var str = this;
    var pos = str.indexOf(de);
    while (pos > -1) {
		str = str.replace(de, para);
		pos = str.indexOf(de);
	}
    return (str);
}