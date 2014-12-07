<h2 id="navContainer" class="nav-tab-wrapper woo-nav-tab-wrapper">
	<a data-nav="general" class="nav-tab nav-tab-active"  >General</a>
	<a data-nav="linkforms" class="nav-tab " >Link / Forms</a>
	<a data-nav="menu"  class="nav-tab " >Menu</a>
	<a data-nav="adminbar"  class="nav-tab " >Admin Bar</a>
</h2>

<form id="live_adminCSS" class="list-table">
	<table cellspacing="10" cellpadding="10">
    	<tr>
        	<th>Style Name</th>
            <td><input type="text" name="styleName" value="<?php echo $this->scss_val('file_base_name'); ?>"   />
			    <input type="hidden" name="current_user" value="<?php echo $current_user->user_login; ?>" />
			</td>
        </tr> 

    </table>
    <hr/>
	<?php 
        require(lac_path.'views/create_new/general.php'); 
        require(lac_path.'views/create_new/linksforms.php');
        require(lac_path.'views/create_new/menu.php');
        require(lac_path.'views/create_new/adminbar.php');
	?>
	 <hr/>
	<table cellspacing="10" cellpadding="10">

   		<tr>
			<td><input data-action="save" type="button" class="button button-primary ajaxWork" value="Save Style" /></td>
			<td><input data-action="view" type="button" class="button button-secondary ajaxWork" value="View Style" /></td>
		</tr>
    </table>
</form> 
