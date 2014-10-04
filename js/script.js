var loading = '<div class="loading_container">'+ 
				'<div class="bubble"></div>'+
				'<div class="bubble"></div>'+
				'<div class="bubble"></div>'+
				'<div class="bubble"></div>'+
			  '</div>';


jQuery(document).ready(function(){
  
var dlg = jQuery("<div id='colorpickersPOP' />").html('<div id="picker"></div>').appendTo("body");
var delcpop = jQuery("<div id='deleteConfirmPOP' />").html(loading).appendTo("body"); 
var ajaxloading = jQuery("<div id='loading' />").html(loading).appendTo("body"); 
var clickedElem = '';	
	jQuery('#navContainer > a').click(function(){
		var open_tab = jQuery(this).attr('data-nav');
		jQuery('#navContainer > a.nav-tab-active').removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		jQuery('.tab.active').removeClass('active').fadeOut('fast',function(){
				jQuery('#'+open_tab).addClass('active').fadeIn('fast');	
				 
		});
	});
	
	
     
jQuery('body').append('<style id="custom_liveCSs"> </style>');
    

	
	dlg.dialog({
		'dialogClass' : 'wp-dialog',
		'modal' : true,
		'autoOpen' : false,
		'closeOnEscape' : true,
		'width' :'auto',
		'title':'Color Picker',
		 beforeClose : function(){ GoAjax('lac_scss_compile'); },
		'buttons' : [ { 'text' : 'Close', 'class' : 'button-primary', 'click' : function() { jQuery(this).dialog('close'); } }]
	});
	
    delcpop.dialog({
        'dialogClass' : 'wp-dialog',
        'modal' : true,
        'autoOpen' : false,
        'closeOnEscape' : true,
        'width' :'auto',
        'title':'Working Please Wait',
        'buttons' : [ { 'text' : 'Close', 'class' : 'button-primary', 'click' : function() { jQuery(this).dialog('close'); } }]
    });
    
     
    
    ajaxloading.dialog({
        'dialogClass' : 'wp-dialog',
        'modal' : true,
        'autoOpen' : false,
        'closeOnEscape' : false,
        'width' :'auto',
        'title':'Loading Css Please Wait',
        'buttons' :''
    });   
    
    jQuery('input.preview_btn').click(function(){
		ajaxloading.dialog('open');
        jQuery.ajax({
            url: jQuery(this).attr('data-css-url')
        }).done(function(msg) {
			ajaxloading.dialog('close');
            jQuery('style#custom_liveCSs').html(msg);
		    
        });    
    });
	
	var colorPicker = jQuery('#picker').colpick({
		flat:true,
		layout:'hex',
		submit:0,
		colorScheme:'light',
		livePreview:true, 
		onChange : function(hsb,hex,rgb,el,bySetColor){ clickedElem.val('#'+hex);  clickedElem.attr('exColor',hex); }
	});	
	
	jQuery('input[data-color=yes]').click(function(){
		clickedElem = jQuery(this);
 		if(jQuery(this).attr('exColor')){ colorPicker.colpickSetColor(jQuery(this).attr('exColor'),false);  }
 		dlg.dialog('open')
	}).each(function(){
        jQuery(this).parent().append('<input type="button" class="clearInput button button-secondary" value="x" />');
         
    });
	
    jQuery('form#live_adminCSS').on('click','input.clearInput',function(){
        jQuery(this).prev().val('');
        GoAjax('lac_scss_compile');
    });
    
	jQuery('.ajaxWork').click(function(){
		
		if(jQuery(this).attr('data-action') == 'view' ){
			GoAjax('lac_scss_compile');
			
		} else if(jQuery(this).attr('data-action') == 'save' ) {
            
            delcpop.html(loading).dialog({'width' :300,'height' : 'auto','title':'Saving Please Wait','buttons' : ''});
            delcpop.dialog('open');
            
            jQuery.ajax({
                url: site_url+"/wp-admin/admin-post.php",
                data:jQuery("form#live_adminCSS").serialize()+'&action=lac_scss_create',
                type:'POST'
            }).done(function(msg) {
                if(msg == 'empty_name'){
                    delcpop.dialog({
                    'title':'Error Saving Style',
                    'buttons' : ''
                    }).html('<h2>Style Name Cannot Be Empty</h2>');                
                } else if(msg == 'saved failed'){
                    delcpop.dialog({
                    'title':'Error Saving Style',
                    'buttons' : ''
                    }).html('<h2>Unable to write css Files</h2>');                   
                } else if(msg == 'saved'){
                    delcpop.dialog({
                    'title':'Successfully Saved',
                    'buttons' : [{'text':'Close','class':'button-primary','click':function(){jQuery(this).dialog('close');}}]
					}).html('<h2>Successfully Saved</h2>');                   
                }

            });
		}
		 
	});
 
    jQuery('.delete').click(function(){
        var del_id = jQuery(this).attr('data-slug');

        var delConfirm = '<h2> Are you sure want to delete "'+del_id+'" Style </h2>'+
                         '<input style="margin-right:30px;"data-del-id="'+del_id+'" type="button" class="button button-primary" id="delete_y"  value="Sure !! " />'+
                         '<input type="button" class="button button-secondary" id="delete_n"  value="No Cancel" />';
                            
        delcpop.html(delConfirm);
        delcpop.dialog('open');
    });
    
    jQuery('div#deleteConfirmPOP').on('click','#delete_n',function(){
        delcpop.dialog('close');
    });
    
    jQuery('div#deleteConfirmPOP').on('click','#delete_y',function(){
        var delID = jQuery(this).attr('data-del-id');
        delcpop.html(loading).dialog({
            'width' :300,
            'height' : 'auto',
            'title':'Deleting Please Wait',
            'buttons' : ''
        });
        
        jQuery.ajax({
            url: site_url+"/wp-admin/admin-post.php",
            data: {action:'lac_scss_delete',slug:delID},
            type:'POST'
        }).done(function(msg) {
            if(msg){
                delcpop.dialog('close')
                jQuery('tr#'+delID).fadeOut('slow',function(){jQuery(this).remove();})
            } else {
                delcpop.html('<h2>Unable to delete style.</h2><h4>Please Try Again later.</h4>');
            }
        });
    });    
}); 






function GoAjax(action){
    jQuery.ajax({
		url: site_url+"/wp-admin/admin-post.php",
		data:jQuery("form#live_adminCSS").serialize()+'&action='+action,
		type:'POST'
    }).done(function(msg) {
        jQuery('style#custom_liveCSs').html(msg);
    });
}