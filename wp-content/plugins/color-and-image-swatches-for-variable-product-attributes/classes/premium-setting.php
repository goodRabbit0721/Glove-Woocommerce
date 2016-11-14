<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$plugin_dir_url =  plugin_dir_url( __FILE__ );

?>
<style>
.premium-box{ width:100%; height:auto; background:#fff;  }
.premium-features{}
.premium-heading{  color: #484747;font-size: 40px;padding-top: 35px;text-align: center;text-transform: uppercase;}
.premium-features li{ width:100%; float:left;  padding: 80px 0; margin: 0; }
.premium-features li .detail{ width:50%; }
.premium-features li .img-box{ width:50%; }

.premium-features li:nth-child(odd) { background:#f4f4f9; }
.premium-features li:nth-child(odd) .detail{float:right; text-align:left; }
.premium-features li:nth-child(odd) .detail .inner-detail{}
.premium-features li:nth-child(odd) .detail p{ }
.premium-features li:nth-child(odd) .img-box{ float:left; text-align:right;}

.premium-features li:nth-child(even){  }
.premium-features li:nth-child(even) .detail{ float:left; text-align:right;}
.premium-features li:nth-child(even) .detail .inner-detail{ margin-right: 46px;}
.premium-features li:nth-child(even) .detail p{ float:right;} 
.premium-features li:nth-child(even) .img-box{ float:right;}

.premium-features .detail{}
.premium-features .detail h2{ color: #484747;  font-size: 24px; font-weight: 700; padding: 0;}
.premium-features .detail p{  color: #484747;  font-size: 13px;  max-width: 327px;}
/**images**/
.pincode-check{ background:url(<?php echo $plugin_dir_url; ?>../images/Enable_Disable_Plugin.png); width:448px; height:72px; display:inline-block; margin-right: 25px; margin-top: -2px; background-repeat:no-repeat;}

.sat-sun-off{ background:url(<?php echo $plugin_dir_url; ?>../images/Give_Your_Own_Title.png); width:515px; height:63px; display:inline-block; background-size:500px auto; margin-right:30px; margin-top: 20px; background-repeat:no-repeat;}

.bulk-upload{ background:url(<?php echo $plugin_dir_url; ?>../images/products_from_Product_Page.png); width:250px; height:322px; display:inline-block; margin-top: 25px; background-repeat:no-repeat;}

.cod-verify{background:url(<?php echo $plugin_dir_url; ?>../images/Many_Products_Single_Page.png); width:548px; height:338px; display:inline-block; margin-right:30px; margin-top: 20px; background-repeat:no-repeat;}

.delivery-date{background:url(<?php echo $plugin_dir_url; ?>../images/Customize_Text_For_total_Price.png); width:624px; height:132px; display:inline-block;margin-top:-6px; background-repeat:no-repeat;}

.number_of_selected_items {background:url(<?php echo $plugin_dir_url; ?>../images/number_selected_items.png); width:548px; height:145px; display:inline-block; margin-right:30px; margin-top: 20px; background-repeat:no-repeat;}

.Set_Thumbnail_Position{background:url(<?php echo $plugin_dir_url; ?>../images/Set_Thumbnail_Position.png); width:525px; height:73px; display:inline-block;margin-top:-6px; background-repeat:no-repeat;}



/*upgrade css*/

.upgrade{background:#f4f4f9;padding: 50px 0; width:100%; clear: both;}
.upgrade .upgrade-box{ background-color: #808a97;
    color: #fff;
    margin: 0 auto;
   min-height: 110px;
    position: relative;
    width: 60%;}

.upgrade .upgrade-box p{ font-size: 15px;
     padding: 19px 20px;
    text-align: center;}

.upgrade .upgrade-box a{background: none repeat scroll 0 0 #6cab3d;
    border-color: #ff643f;
    color: #fff;
    display: inline-block;
    font-size: 17px;
    left: 50%;
    margin-left: -150px;
    outline: medium none;
    padding: 11px 6px;
    position: absolute;
    text-align: center;
    text-decoration: none;
    top: 36%;
    width: 277px;}

.upgrade .upgrade-box a:hover{background: none repeat scroll 0 0 #72b93c;}

.premium-vr{ text-transform:capitalize;} 
.premium-box.premium-box-head {
    background: #eae8e7 none repeat scroll 0 0;
    height: 500px;
    text-align: center;
    width: 100%;
}
.premium-box .pho-upgrade-btn {
    text-align: center;
}

.premium-box .pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}
.premium-box {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    height: auto;
    width: 100%;
}
.premium-box-head {
    background: #eae8e7 none repeat scroll 0 0;
    height: 500px;
    text-align: center;
    width: 100%;
}
.main-heading {
    background: #fff none repeat scroll 0 0;
    margin-bottom: -70px;
    text-align: center;
}
.main-heading h1 {
    margin: 0;
}
.main-heading img {
    margin-top: -200px;
}
.premium-box-container .description:nth-child(2n+1) {
    background: #fff none repeat scroll 0 0;
}
.premium-box-container .description {
    display: block;
    padding: 35px 0;
    text-align: center;
}
.premium-box-container .pho-desc-head::after {
    background: rgba(0, 0, 0, 0) url(<?php echo $plugin_dir_url; ?>../assets/images/head-arrow.png) no-repeat scroll 0 0;
    content: "";
    height: 98px;
    position: absolute;
    right: -40px;
    top: -6px;
    width: 69px;
}
.premium-box-container .pho-desc-head {
    margin: 0 auto;
    position: relative;
    width: 768px;
}
.premium-box-container .pho-desc-head h2 {
    color: #02c277;
    font-size: 28px;
    font-weight: bolder;
    margin: 0;
    text-transform: capitalize;
	line-height:30px;
}
.pho-plugin-content {
    margin: 0 auto;
    overflow: hidden;
    width: 768px;
}
.pho-plugin-content p {
    color: #212121;
    font-size: 18px;
    line-height: 32px;
}
.premium-box-container .description:nth-child(2n+1) .pho-img-bg {
    background: #f1f1f1 url(<?php echo $plugin_dir_url; ?>../assets/images/image-frame-odd.png) no-repeat scroll 100% top;
}
.description .pho-plugin-content .pho-img-bg {
    border-radius: 5px 5px 0 0;
    height: auto;
    margin: 0 auto;
    padding: 70px 0 40px;
    width: 750px;
}
.pho-plugin-content img {
    max-width: 100%;
    width: auto;
}
.premium-box-container .description:nth-child(2n) {
    background: #eae8e7 none repeat scroll 0 0;
}
.premium-box-container .description {
    display: block;
    padding: 35px 0;
    text-align: center;
}
.premium-box-container .description:nth-child(2n) .pho-img-bg {
    background: #f1f1f1 url(<?php echo $plugin_dir_url; ?>../assets/images/image-frame-even.png) no-repeat scroll 100% top;
}
.premium-box .pho-upgrade-btn {
    text-align: center;
}
.pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}
</style>
<?php $plugin_dir_url = plugin_dir_url(dirname(__FILE__));?>

<div class="premium-box">

<div class="premium-box-head">
           <div class="pho-upgrade-btn">
		   
<!--<p> Switch to the premium version of Woocommerce Check Pincode/Zipcode for Shipping and COD to get the benefit of all features! </p>
--><a target="_blank" href="https://www.phoeniixx.com/product/color-image-swatches-woocommerce/"><img src="<?php echo $plugin_dir_url;?>assets/images/premium-btn.png"></a>

</div>
</div>
<div class="main-heading">
           <h1> <img src="<?php echo $plugin_dir_url;?>assets/images/premium-head.png">
           
          </h1>
</div>
<div class="premium-box-container">
	
	
	<div class="description">
                <div class="pho-desc-head">
                <h2>General Options</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>
						Option to create swatches using <b>colors.</b><br>

						Option to create swatches using <b>images.</b><br>

						Option to create swatches using <b>icons</b>, grid of 300 icons library is available with

						this option.<br>

						Option to create swatches using <b>text</b>.<br>

						Attributes can be shown in the <b>dropdown</b> by choosing option <b>None</b></b>. 
						</p>
                        
                    </div>
    </div>	
	
	
	<div class="description">
                <div class="pho-desc-head">
                <h2>Stylizing options</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>
						<b>Swatch Border Style</b>: option is available in square and circle style,

<b>Swatch Size</b>,

<b>Default Swatch Border Color</b>,

<b>Active Swatch Border Color</b>,

<b>Swatch Color</b>,

<b>Swatch Hover Color</b>,	</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url;?>assets/images/screenshot--2.png">
                        </div>
                    </div>
    </div>

<div class="description">
                <div class="pho-desc-head">
                <h2>You can add the swatches globally</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>Go to <b>color and image swatch tab</b>&gt; select the <b>type&gt;</b> <b>taxonomy color and images</b> is to

be chosen to <b>add the swatches Globally.</b></p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url;?>assets/images/screenshot--3.png">
                        </div>
                    </div>
</div>

<div class="description">
                <div class="pho-desc-head">
                <h2>You can add the swatches on per product bases</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>Go to <b>color and image swatch tab</b>&gt; select the <b>type</b>&gt; <b>custom color and images</b> is to be

chosen to add the swatches type on <b>per product basis</b> &gt; <b>add the swatch type.</b></p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url;?>assets/images/screenshot--4.png">
                        </div>
                    </div>
</div>
<div class="description">
                <div class="pho-desc-head">
                <h2>Frontend view of the product with different swatches used in it</h2></div>
                
                    <div class="pho-plugin-content">
                        
                        <br><div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url;?>assets/images/screenshot--5.png">
                        </div>
                    </div>
</div>


</div>
<div class="pho-upgrade-btn">
        <a target="_blank" href="https://www.phoeniixx.com/product/color-image-swatches-woocommerce/"><img src="<?php echo $plugin_dir_url;?>assets/images/premium-btn.png"></a>
</div>

</div>
