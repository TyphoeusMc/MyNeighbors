<html代码模板> 
<div>
	<button class="but" onclick="but()">点击我查询消息</button>
</div>
<div style="display: none;" id="divdisplay">
	<div class="mask" style="display: block;"></div>
	<div style="top: 132.5px; left: 569.5px; display: block;" class="modal-login">
		<div class="modal-login-tit">
			<h2>请关注公众号进行支付</h2>					
			<i class="icon icon-close"></i>					
			<a href="javascript:;" class="close" shape="rect">
			<div class="close"  onclick="closeon()" style="color: red;">关闭</div>	</a>				
		</div>
		<div class="login-box">
			<div class="login-bd">
				<div class="login-code" style="display: block;">
					<div id="J-login-code-con" class="login-code-con" style="display: block;">
						<div class="login-code-main">
						<div class="code-pic" style="left: 80px;"><img id="J-qrImg" src="img/wchat.jpg">
						<div id="J-code-error-mask" style="display: block;" class="code-error-mask"></div>
						</div>
					</div>
				</div>
				请关注我的公众号||请关注我的公众号||请关注我的公众号||
			</div>
		</div>
		<div class="login-ft">
			<p>1、你好用户，请关注我的公众号，进行支付</p>
			<p>2、weipay提供网络支持</p>						
		</div>
	</div>
</div>
</div>
<script type="text/javascript" src="indeed.js" ></script>		
<css代码模板> 
div, form, img, ul, ol, li, dl, dt, dd {
    margin: 0;
    padding: 0;
    border: 0;
}
.but{
	background-color: greenyellow;
	margin-left:100px ;
	margin-top:100px ;
	width: 120px;
	height: 50px;
	
}
.mask {
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background: #000;
    opacity: .5;
    z-index: 16000;
}
.modal-login {
    position: absolute;
    top: 0;
    left: 0;
    width: 380px;
    z-index: 19000;
    font-size: 14px;
    -wekbit-box-sizing: border-box;
    -o-box-sizing: border-box;
    -ms-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.modal-login .modal-login-tit {
    height: 34px;
    line-height: 34px;
    padding: 0 20px;
    background: #f8f8f8;
    position: relative;
}
.modal-login .modal-login-tit .close {
    position: absolute;
    top: 0;
    right: 0;
    width: 34px;
    height: 34px;
    text-align: center;
    line-height: 34px;
}
.modal-login a {
    color: #333;
}
.icon {
    font-family: "icon" !important;
    font-size: 16px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.login-box {
    background: #fff;
    width: 380px;
}
.login-box .login-hd {
    height: 78px;
    line-height: 78px;
    padding: 0 30px;
}
.login-box .login-hd .login-hd-code {
    margin-right: 60px;
}

.login-box .login-hd li {
    float: left;
    width: 130px;
    text-align: center;
    position: relative;
}
.login-box .login-hd li {
    float: left;
    width: 130px;
    text-align: center;
    position: relative;
}
.modal-login .login-box .login-hd {
    height: 48px;
    line-height: 48px;
}

.login-box .login-hd {
    height: 78px;
    line-height: 78px;
    padding: 0 30px;
}
.login-code .login-code-loading {
    text-align: center;
    width: 100%;
    height: 40px;
    line-height: 40px;
    padding: 115px 0;
}
.login-code .code-pic {
    position: absolute;
    top: 20px;
    left: 80px;
    width: 160px;
    height: 160px;
    border: 1px solid #efefef;
}
.login-code .code-error-mask {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
   /* background: #000;*/
    opacity: .65;
    z-index: 10;
    display: none;
}
.login-code .login-code-main {
    height: 200px;
    position: relative;
}
.login-code .login-code-con {
    height: 270px;
    padding: 0 30px;
}

.modal-login *, .modal-login *:before, .modal-login *:after {
    -wekbit-box-sizing: border-box;
    -o-box-sizing: border-box;
    -ms-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
element.style {
    top: 114.5px;
    left: 371px;
}

.modal-login {
    position: absolute;
    top: 0;
    left: 0;
    width: 380px;
    z-index: 19000;
    font-size: 14px;
    -wekbit-box-sizing: border-box;
    -o-box-sizing: border-box;
    -ms-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
/*底部样式*/
.login-ft {
    padding: 10px 20px 15px;
    font-size: 15px;
    color: blue;
    border-top: 1px solid #efefef;
}
/*内置照片缩写*/
.login-code .code-pic img {
    display: block;
    width: 158px;
    height: 158px;
}

.icon {
    font-family: "icon"!important;
    font-size: 16px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
<js模板> 
function but(){	
	var div=document.getElementById("divdisplay");
	div.style.display='block';
}
function closeon(){
	var div=document.getElementById("divdisplay");
	div.style.display='none';
}