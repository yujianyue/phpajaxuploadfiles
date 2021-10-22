<?php
error_reporting(0);//容错解决部分自建空间报错
//1MB带宽演示页面： http://ewuyi.net/php/chuanlide.php
$title="php单文件实现ajax多文件上传带进度条完整示范";
$desc ="可设定上传类型，上传大小限制，上传文件个数限制;<br>";
$desc.="JS前端判断上传类型，大小等，节约服务器资源;<br>";
$desc.="独立单文件不调用第三方插件，比如jquery.js,SWFUpload.swf,SWFUpload.js等;<br>";
$isup = ".csv|.txt|.jpg|.gif|.png|.rar|.zip|.doc|.xls|.xlsx|.mp3|.mp4"; //修改可上传格式
$lenx = 5120; //修改上传文件最大值，单位KB
$lenf = 20; //修改上传文件个数限制，太多可能会导致手机或电脑奔溃
$updir = "updir";
function getext($file){
$info = pathinfo($file);
return $info['extension'];
}
if($_GET["act"]=="up"){
//echo json_encode($_FILES["pics"]);
  $filex = $_FILES['pics'];
if ($filex["error"] > 0){
  $errs = $filex['error'];
  echo "<b>错误{$errs}:</b>";
  switch ($errs) {
case 1:  echo '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值'; break;
case 2:  echo '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值'; break;
case 3:  echo '上传异常,文件只有部分被上传'; break;
case 4:  echo '文件空白或者说没有文件被上传'; break;
case 6:  echo '上传的临时文件丢失'; break;
case 7:  echo '文件写入失败建议检查文件夹读写权限'; break;
}
}else{
  $tape = getext($filex["name"]);
if(!stristr("|{$isup}|","|.{$tape}|")){ exit("<b>上传失败：</b>文件名后缀[{$tape}]不支持!");}
if($filex["size"]>$lenx*1024){ exit("<b>上传失败：</b>文件大小超过允许值{$lenx}KB!");}
  $fileName = date("YmdHis")."_".uniqid().".".$tape;
if(!is_dir("./$updir/")) {
if(!mkdir("./$updir/", 0777, true)) {
 exit("<span>转存失败：</span>创建文件夹失败！");
}
}
move_uploaded_file($filex["tmp_name"], "./$updir/".$fileName);
if (file_exists("./$updir/".$fileName)){
 exit("<span>上传成功：</span>更名为：".$fileName);
}else{
 exit("<span>转存失败：</span>请检查文件夹读写权限！");
}
}
exit();
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo $title; ?></title>
<meta name="author" content="yujianyue, admin@ewuyi.net">
<meta name="copyright" content="www.12391.net">
<script>
var $=function(node){
return document.getElementById(node);
}
function $(objId){
return document.getElementById(objId);
}
function GetRequest(Url,ia,GetFunction){
if(window.ActiveXObject){
var xpost = new ActiveXObject("Microsoft.XMLHTTP");
}else{
var xpost = new XMLHttpRequest();
}
xpost.onreadystatechange = function(){
if(xpost.readyState == 4){
if(xpost.status == 200){
GetFunction(xpost.responseText);
}else{
GetFunction(404);
}
}
}
xpost.open("post",'?act=up&vi='+ia,true);
//xpost.setRequestHeader("Content-type","application/x-www-form-urlencoded;charset-UTF-8");
xpost.upload.onprogress = function(evt) {
per = Math.floor((evt.loaded / evt.total) * 100) + "%";
$('per'+ia).style.width = per;
$('per'+ia).innerHTML = per+"";
}
xpost.send(Url);
}
window.onload = function () {
input = $("fielinput");
if (typeof (FileReader) === 'undefined') {
$("tips").innerHTML = "抱歉，请使用chrome,firefox等现代浏览器，国产浏览器请使用急速模式！";
input.setAttribute('disabled', 'disabled');
} else {
input.addEventListener('change', readFile, false);
}
}
function postFile(filea,ib,finame) {
console.log("files:"+ib,filea);
var SendUrl = new FormData();
    SendUrl.append('pics',$('fielinput').files[ib]);
GetRequest(SendUrl,ib,function(GetText){
if(GetText == 404){
$("tips"+ib).innerHTML += "<br><b>上传失败：</b>通讯异常!";
}else{
$("tips"+ib).innerHTML += "<br>"+GetText;
}
});
}
function readFile() {
files = $("fielinput").files;
$("tips").innerHTML = "";
if(files.length><?php echo $lenf; ?>){
$("tips").innerHTML = "<b>全部未上传：</b>文件数超<?php echo $lenf; ?>个!";
return false;
}
for(ii=0; ii<files.length; ii++){
ia=ii;
finame = files[ia].name;
fisize = files[ia].size;
if (fisize>1) {
$("tips").innerHTML += "<div class=\"tips\"><div id=\"tips"+ia+"\"><span>["+ia+"]</span> "+finame+"</div><div class=\"per\" id=\"per"+ia+"\"></div></div>\r\n";
if (!/(<?php echo $isup; ?>)$/.test(finame)){
$("tips"+ia).innerHTML += "<br><b>未上传：</b>后缀格式不支持!";
continue;
}
if (fisize><?php echo $lenx; ?>*1024) {
$("tips"+ia).innerHTML += "<br><b>未上传：</b>文件大小超<?php echo $lenx; ?>kB!";
continue;
}
filea = files[ia];
postFile(filea,ia,finame);
}
}
$("fielinput").value="";
} 
</script>
<style type="text/css">
*{margin:0;padding:0;font-size:14px;line-height:150%;font-family:"microsoft yahei",SimHei;}
#tips,#file{width:95%;margin:10px auto;padding:8px;background:gray;}
.tips {margin:5px auto;width:calc(95%-12px);background:lightgray;}
.per {width:0;line-height:100%;background:lightgreen;text-align:center;display:block;}
b {color:red;font-size:18px;font-weight:bold;}
span {color:green;font-size:18px;font-weight:bold;}
</style>
</head>
<body> 
<div id="file"><input type="file" id="fielinput" multiple="multiple" /></div>
<div id="tips">
<b>操作说明：</b>点上边按钮选择要上传文件即可!<br>
<b>支持上传：</b><?php echo $isup;?>后缀文件!<br>
<!--b>上传限制：</b>文件<?php echo $lenf; ?>个以内，每个文件<?php echo $lenx;?>KB以内!<br-->
<h1><?php echo $title; ?></h1>
<?php echo $desc; ?>
</div>
</body>
</html>
