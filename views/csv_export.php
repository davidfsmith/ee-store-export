<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="orders.csv"');
?>
A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ
order_id,,order_item_id,billing_name,billing_email,billing_phone,billing_address1,billing_address2,billing_address3,billing_postcode,billing_country,,order_custom1,,shipping_name,shipping_address1,shipping_address2,shipping_address3,shipping_postcode,shipping_country,item_qty,,sku,title,,price_inc_tax,item_total,delivery_charge,,,,,,,,,,,,,,,shipping_method,,price_inc_tax,order_shipping
<?php
if (sizeof($orders > 0)):
    foreach ($orders as $order) {
?>
<?=$order->order_id?>,,<?=$order->order_item_id?>,<?=$order->shipping_name?>,<?=$order->order_email?>,<?=$order->billing_phone?>,<?=$order->shipping_address1?>,<?=$order->shipping_address2?>,<?=$order->shipping_address3?>,<?=$order->shipping_postcode?>,<?=$order->shipping_country?>,,<?=$order->order_custom1?>,,<?=$order->billing_name?>,<?=$order->billing_address1?>,<?=$order->billing_address2?>,<?=$order->billing_address3?>,<?=$order->billing_postcode?>,<?=$order->billing_country?>,<?=$order->item_qty?>,,<?=$order->sku?>,<?=$order->title?>,,<?=$order->price_inc_tax?>,<?=$order->item_total?>,<?=$order->base_rate?>,,,,,,,,,,,,,,,<?=$order->shipping_method_name?>,,<?=$order->item_total?>

<?php
    }
endif;
?>