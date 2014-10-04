<?php
global $live_admin_customizer;
$files = $live_admin_customizer->css_files;
if(!empty($files)){
?>
<table cellspacing="15" cellpadding="15">
        <thead>
            <tr>
                <th>Theme Name</th>
                <th>Created Time</th>
                <th>Colour Schema</th>
                <th>Created By</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($files as $fk => $f){
                    $MetaData =  $live_admin_customizer->file_meta($f);
                   
		    		$exColor = explode(",",$MetaData['colors']); 
		    		$removeColor = array('#000000','#ffffff');
		    		$actualColor = array_filter(str_replace($removeColor,'',$exColor));
                    $actualDivColor = '';  
                    foreach($actualColor as $c ){
                        $actualDivColor .= '<div class="colorBox" style="background-color:'.$c.'"> </div>';
                    }
                    
                    $css_url = lac_style_url.$fk.'/'.$fk.'.css';
                     
                    echo '<tr id="'.$MetaData['slug'].'">
                    <td>'.$MetaData['name'].'</td>
                    <td>'. date('Y/m/d | h:i s a',$MetaData['ctime']).'</td>
                    <td class="colorschema"> <div class="colorSchema"> '.$actualDivColor.' </div> </td>
                    <td>'.$MetaData['cby'].'</td>
                    <td><input data-css-url="'.$css_url.'" data-slug="'.$MetaData['slug'].'"  type="button" class="preview_btn button button-primary" value="Preview" /> 
                        <input data-slug="'.$MetaData['slug'].'"  type="button" class="delete button button-secondary" value="X" /></td>
                    </tr>';                    

                }

            ?>

        </tbody>
    </table> 
<?php
} else {
?>
<h2>No Themes Created By You.</h2>
<h3>Please <a href="<?php echo admin_url('admin.php?page=live-admin-customizer'); ?>"> Create One Now. </a></h3>
<?php
}
?>