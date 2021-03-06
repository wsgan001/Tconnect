tAssistance.ContraintMaker = function(){
	
	var layout = new tAssistance.dom.NavTab1("Contraints");
		
	var models = [
	             {
	            	    "id": "oza_model",
	            	    "obsels": [
	            	      {
	            	        "id": "oze_idg",
	            	        "properties": {
	            	          "user": "string",
	            	          "idSession": "string",
	            	          "infos_userAgent": "string"
	            	        }
	            	      },
	            	      {
	            	        "id": "oze_view",
	            	        "properties": {
	            	          "user": "string",
	            	          "idSession": "string",
	            	          "infos_userAgent": "string"
	            	        }
	            	      }
	            	    ]
	            	  }
	            	];
	
	var contraints = [
	     "number", "string", "values"
	                  
	];
	
	var form = new tAssistance.dom.ContraintForm(models, contraints);
	//var tbody = table.querySelector("tbody");
	var panel_body = layout.querySelector("[placeholder='navtab-body']");
	panel_body.appendChild(form);
	
//	for(var i in icons){
//		var icon = icons[i];
//		icon;
//		
//		var row = tAssistance.dom.IconRow(icon);
//		
//		var chk_box = row.querySelector("input[type='checkbox']");
//		
//		chk_box.onclick = function(){
//
//		};		
//		
//		tbody.appendChild(row);		
//	}	
	
	return layout;
};

tAssistance.OzaTimelineConfigMaker = function(){
	
	var layout = new tAssistance.dom.PanelLayout();
	
	layout.querySelector("div.panel-title").innerHTML = "Timeline";
	//$(layout.querySelector("div.panel-body")).css("height","300px");
	//$(layout.querySelector("div.panel-body")).css("overflow","scroll");
		
	var ul = new tAssistance.dom.OzaTimelineSetting("aaa1");
	layout.querySelector("div.panel-body").appendChild(ul);
	
	return layout;
};

tAssistance.FilterEditorMaker = function(filter){
	
	var table = new tAssistance.FilterTableMaker(filter);
	
	var layout = new tAssistance.dom.PanelLayout("aa3");
	layout.querySelector("div.panel-title").innerHTML = "Filter Setting";
	//$(layout.querySelector("div.panel-body")).css("height","300px");
	//$(layout.querySelector("div.panel-body")).css("overflow","scroll");
	layout.querySelector("div.panel-body").appendChild(table);
	
	return layout;
};

tAssistance.FilterTableMaker = function(filter){
	var pfilters = filter.pfilters;
	
	var table = new tAssistance.dom.FilterTable();
	
	for(var i in pfilters){
		var pfilter = pfilters[i];
		var tbody = table.querySelector("tbody");
		
		var row = tAssistance.dom.PFilterRow(pfilter);
		
		var chk_box = row.querySelector("input[type='checkbox']");
		
		chk_box.onclick = function(){
			var chked = this.checked;
			
			pfilter.active = chked;
			
			var store = new tAssistance.Store();
			store.updatePFilter("hoang","velement1", "pfilter1", { "active": chked });
		};
				
		tbody.appendChild(row);
	}
	return table;
};

tAssistance.OzaPFilterRowMaker = function(pfilter){
	
	
	
};

tAssistance.PFilterTemplateControlMaker = function(params){
	
	var templates = params.templates;
	var afterClick = params.afterClick;
	
	var control = new tAssistance.dom.PFilterTemplateControl(templates);
	var btn = control.querySelector("div[type='button']");
	
	var menu = control.querySelector("ul");
	
	for(var i in templates){
		var templ = templates[i];
		
		var templ_dom = new tAssistance.dom.TemplRow(templ);
				
		templ_dom.onclick = function (e){
			btn.innerHTML = e.target.textContent;
			if(afterClick){
				afterClick(e.target.textContent);
			}
		};
		
		menu.appendChild(templ_dom);
	}
	
	return control;
};

tAssistance.PFilterPanelBodyMaker = function(params){
	
	var pfilter = params.pfilter;
	var user_id = params.user_id;
	var velement_id = params.velement_id;
	var pfilter_id = params.pfilter_id;
		
	var pfilter_dom = new tAssistance.dom.PFilter();
	
	var buttons_params = params;
		
	var btns_control = new tAssistance.PFilterBtnControlMaker(buttons_params);
	
	pfilter_dom.appendChild(btns_control);
	
	var properties = ["id","string", "begin"];
	
	var property_control = new tAssistance.dom.PropertySelectControl(properties);
	
	pfilter_dom.appendChild(property_control);
	
	var templates = ["string","number","values"];
	
	var template_params = {
		"templates": templates,
		"afterClick": function(value){
			var params = {
				"template": value,
				"pfilter": pfilter
			};
			
			var control = new tAssistance.PFilterControlMaker(params);
			
			var control_dom_old = pfilter_dom.querySelector(".pfilter-control");
			control_dom_old.parentNode.replaceChild(control, control_dom_old);
		}
	};
	
	var template_control = new tAssistance.PFilterTemplateControlMaker(template_params);
	
	pfilter_dom.appendChild(template_control);
	
	var control_params = {
			"pfilter": pfilter,
			"template": "string"
		};
	
	var pfilter_control = new tAssistance.PFilterControlMaker(control_params);
	
	pfilter_dom.appendChild(pfilter_control);
		
	return pfilter_dom;	
};

tAssistance.PFilterBinder = function(params){
	var pfilter = params.pfilter;
	var el = params.element;
	
	
	
	
	
	
	
};

tAssistance.PFilterPanelMaker = function(params){
	
	var pfilter = params.pfilter;
	//var template = params.template;
		
	var body = new tAssistance.PFilterPanelBodyMaker(params);
	
	var layout = new tAssistance.dom.PanelLayout("aa3");
	layout.querySelector("div.panel-title").innerHTML = "Timeline: Attribute Contraints";
	layout.querySelector("div.panel-body").appendChild(body);
	
	return layout;
};