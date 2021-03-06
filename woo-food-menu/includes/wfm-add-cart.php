<?php
require_once('../../../../wp-blog-header.php');
header('HTTP/1.1 200 OK');
global $wpdb;
global $woocommerce;

  ini_set('display_errors','Off'); 
  //$vid=$_POST['wfm_prod_var_id'];
  $pid=$_POST['wfm_prod_id'];
  $vid=$_POST['wfm_prod_var_id'];
  $pqty=$_POST['wfm_prod_qty'];
 
  if($vid==0){    
	$product = get_product($pid);
    $bool=$product->is_sold_individually();
    if($bool==1){
      $chk_cart=wfm_check_cart_item_by_id($pid);
      if($chk_cart==0){
        echo 'Already added to cart';
        exit;
      }
    }
  }else{
	$product = get_product($vid);
    $bool=$product->is_sold_individually();
    if($bool==1){      
      $chk_cart=wfm_check_cart_item_by_id($vid);
      if($chk_cart==0){
        echo 'Already added to cart';
        exit;
      }
    }
  }

  $stock=$product->get_stock_quantity();
  $availability = $product->get_availability();
  
  if($availability['class']=='out-of-stock'){
    echo 'Out of stock';
    exit;
  }
       
  if($stock!=''){
    	foreach($woocommerce->cart->cart_contents as $cart_item_key => $values ) {
        $c_item_id='';
        $c_stock='';
        if($values['variation_id']!=''){
          $c_item_id=$values['variation_id'];
        }else{
          $c_item_id=$values['product_id'];
        }
        $c_stock=$values['quantity']+$pqty;
        
        if($vid==0 && $pid==$c_item_id && $c_stock>$stock){
          $product = get_product($pid);		  
          echo 'You have cross the stock limit';
          exit;
        }else if($vid==$c_item_id && $c_stock>$stock){
          $product = get_product($vid);
          echo 'You have cross the stock limit';
          exit;
        }        
	   }    
  }

  if($vid==0){
    $z=$woocommerce->cart->add_to_cart($pid,$pqty,null, null, null );
  }else{    
    $z=$woocommerce->cart->add_to_cart($pid, $pqty, $vid, $product->get_variation_attributes(),null);
  }  
  echo '1';  
  exit;

?>