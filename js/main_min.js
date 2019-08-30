

function setTextToElement(_element,_text) {
if (!_element) return console.error("- setTextToElement: the element (",_element,") doesn't exist.");
_element.innerHTML="";
let a=document.createElement('a');
a.text=String(_text);
_element.append(a);
}

function inArray(arr,item) {
for (let i=0; i < arr.length; i++)
{
if (arr[i] == item)
{
return true;
}
}
return false;
}
function _InfoMenu() {
let HTML={
projectList:projectListHolder,
pages:$(".infoMenuPage")
}

this.openState=true;
this.pageIndex=0;

this.close=function() {
this.openState=false;

document.body.classList.add("infoMenuHidden");

infoMenu.classList.add("hide");
$(".button.infoMenuIcon").show();
}

this.open=function() {
this.openState=true;

document.body.classList.remove("infoMenuHidden");
infoMenu.classList.remove("hide");

$(".button.infoMenuIcon").hide();
}

this.openPageByIndex=function(_index) {
let curPage=HTML.pages[parseInt(_index)];
if (!curPage) return;
this.pageIndex=parseInt(_index);

for (let i=0; i < HTML.pages.length; i++)
{
HTML.pages[i].classList.add("hide");
}
curPage.classList.remove("hide");
}


this.createItemsByList=function(_array) {
HTML.projectList.innerHTML="";
for (let i=0; i < _array.length; i++)
{
this.addItem(_array[i]);
}
}

this.addItem=function(_info) {
let startIndex=HTML.projectList.children.length;
let html='<div class="projectItem">'+
'<img class="headHolder">' +
'<div class="headerText titleHolder preventTextOverflow"></div>' +
'</div>';
HTML.projectList.insertAdjacentHTML("beforeend",html);

html=HTML.projectList.children[startIndex];


setTextToElement(html.children[1],_info.title);
html.children[0].setAttribute("src",_info.imageUrl);

html.onclick=function () {_info.onclick(_info);}
html.addEventListener("click",function () {InfoMenu.onItemClick(_info);})

if (!_info.typeName) return;
html.insertAdjacentHTML("beforeend",'<div class="typeName headerText preventTextOverflow"></div>');
setTextToElement(html.children[2],_info.typeName);
}


this.onItemClick=function() {};



document.onkeyup=function(_e) {
if (_e.key != "Escape") return;

_e.preventDefault();
if (InfoMenu.pageIndex == 1)return InfoMenu.openPageByIndex(0);
if (InfoMenu.openState) return InfoMenu.close();
}
}







function _InfoMenu_mapJsExtender() {
_InfoMenu.call(this);
let Inheriter=new _InfoMenu();
let HTML={
builderHeader:$(".infoMenuPage .text.subHeader")[0],
descriptionHeader:$(".infoMenuPage .text.subHeader")[1],
imagesHeader: $(".infoMenuPage .text.subHeader")[2],
netherPortalButton: $(".infoMenuPage .netherPortalButton")[0],
}



this.addItem=function (_info) {
if (_info.displayInList === false) return;
_info.imageUrl= getHeadUrl(_info);
_info.typeName= _info.type ? _info.type.name : "";
_info.onclick=function () {
InfoMenu.openProjectPageByTitle(_info.title);
}

return Inheriter.addItem(_info);
}


this.openProjectPageByTitle=function(_title) {
let item=Server.getItemByTitle(_title);
if (!item) return;
if (!this.openState) this.open();

updateHeaderVisibility(item);

setTextToElement(projectPage_titleHolder,item.title);
setTextToElement(projectPage_coordHolder,"Coords: ("+item.coords.x+","+item.coords.z+")");
if (item.builders) setTextToElement(projectPage_builderNames,item.builders.join(","));

setDescriptionText(item.description);
addImagesToPage(item.images);

this.openPageByIndex(1);
}

this.goThroughPortal=function(_title) {
window.location.replace("nether.php?project="+_title);
}

function updateHeaderVisibility(_item) {
HTML.builderHeader.style.display="block";
if (!_item.builders || _item.builders.length == 0) HTML.builderHeader.style.display="none";

HTML.descriptionHeader.style.display="block";
if (!_item.description) HTML.descriptionHeader.style.display="none";

HTML.imagesHeader.style.display="block";
if (!_item.images || _item.images.length == 0) HTML.imagesHeader.style.display="none";


HTML.netherPortalButton.style.display="block";
if (!_item.dimensionLink) HTML.netherPortalButton.style.display="none";
HTML.netherPortalButton.onclick=function() {
InfoMenu.goThroughPortal(_item.dimensionLink);
};
}


function setDescriptionText(_text) {
projectPage_description.innerHTML="";
if (!_text) return false;

let descriptionTextLines=_text.split("\n");
for (line of descriptionTextLines)
{ 
let lineHolder=document.createElement("div");
setTextToElement(lineHolder,line);
lineHolder.innerHTML += "<br>";
projectPage_description.append(lineHolder);
}
}


function addImagesToPage(_imageUrls) {
projectPage_imageHolder.innerHTML="";
if (!_imageUrls) return false;

for (let i=0; i < _imageUrls.length; i++)
{
projectPage_imageHolder.innerHTML += "<img class='infoImage'>";
let image=projectPage_imageHolder.children[i];
image.setAttribute("src",_imageUrls[i]);
}
}



function getHeadUrl(_info) {
let headName="";
if (_info.builders) headName=_info.builders[0]; 
if (_info.builders && _info.builders.length > 1) headName="MHF_Exclamation";
if (_info.customHead) headName=_info.customHead;

return "heads.php?type=avatar&username="+headName;
}
}


this._map=function () {
let This=this;
let canvas=document.getElementById("mapCanvas");
let ctx=canvas.getContext("2d");
let mapHolder=document.getElementById("mapHolder");
let clickboxes=[];
let points = null;

let factor=1;

this.settings={
maxZoom: 10,
zoomStepSize: 1,
animationSpeed: 0
}
this.init=function (_points,_factor) {
points = _points;
factor=_factor;

for (i=0; i < _points.length; i++) {
registerPoint(_points[i]);
}

this.panToItem(_points[0]);

document.getElementById("mapCanvas").addEventListener("click",function (e) {
let mouseX=(e.x+mapHolder.scrollLeft)/(mapHolder.scrollWidth-390*InfoMenu.openState);
let mouseY=(e.y+mapHolder.scrollTop)/mapHolder.scrollHeight;
let x=mouseX*canvas.width;
let y=mouseY*canvas.height;

Map.handleClick(x,y);
});

document.addEventListener("mousemove",function (e) {
let mouseX=(e.x+mapHolder.scrollLeft)/(mapHolder.scrollWidth-390*InfoMenu.openState);
let mouseY=(e.y+mapHolder.scrollTop)/mapHolder.scrollHeight;
let x=Map.DOMToMC(mouseX*canvas.width);
let y=Map.DOMToMC(mouseY*canvas.height);

document.getElementById("current_x").innerHTML=Math.round(x);
document.getElementById("current_z").innerHTML=Math.round(y);
});

document.addEventListener("keydown",function (_e) {
if (_e.key == "+")
Map.zoomIn();
if (_e.key == "_")
Map.zoomOut();
if (_e.key == "+" || _e.key == "_")
_e.preventDefault();
});
}

this.redrawPoints = function() {
points.forEach(function(_point, index) {
let x=This.MCToDOM(_point.coords.x);
let z=This.MCToDOM(_point.coords.z);
let username="ddrl46";
if (_point.builders && _point.builders.length == 1)
username=_point.builders;
if (_point.builders && _point.builders.length > 1)
username="MHF_Chest";
if (_point.customHead)
username=_point.customHead;
let colour="#006bed";
if (_point.type.name == "Farm")
colour="#8200ed";
if (_point.customPin)
colour=_point.customPin;
drawPoint(x,z,_point.type.radius,username,colour,_point.displayPoint);
});
}

function registerPoint(_point) {
let x=This.MCToDOM(_point.coords.x);
let z=This.MCToDOM(_point.coords.z);

let username="ddrl46";
if (_point.builders && _point.builders.length == 1)
username=_point.builders;
if (_point.builders && _point.builders.length > 1)
username="MHF_Chest";
if (_point.customHead)
username=_point.customHead;

let colour="#006bed";
if (_point.type.name == "Farm")
colour="#8200ed";
if (_point.customPin)
colour=_point.customPin;

drawPoint(x,z,_point.type.radius,username,colour,_point.displayPoint);

if (_point.clickable == false || _point.displayPoint == false)
return;

clickboxes.push({
point: _point,
x: x-24,
y: z-60,
rx: x+24,
ry: z
});
}

function drawPoint(x,z,radius,username,colour,display) {
let r=20;
ctx.fillStyle="white";
ctx.strokeStyle="white";
ctx.lineWidth=2;

if (radius) {
ctx.beginPath();
ctx.strokeStyle=colour;
ctx.fillStyle=colour;
ctx.arc(x,z,radius/4,0,2*Math.PI);
ctx.stroke();
ctx.globalAlpha=0.2;
ctx.fill();
ctx.globalAlpha=1;
}

if(display == false)
return;

let img=new Image();
img.onload=function () {
ctx.drawImage(img,x-16,z-56,32,32);
};

img.src="heads.php?type=head&scale=2&username="+username;

ctx.fillStyle="white";
ctx.beginPath();
ctx.moveTo(x,z);
ctx.lineTo(x-r,z-2*r-1);
ctx.lineTo(x,z-2*r-1);
ctx.fill();

let grd=ctx.createLinearGradient(x,z-2*r-1,x+0.5*r,z);
grd.addColorStop(0,"white")
grd.addColorStop(1,"#aaa");
ctx.fillStyle=grd;
ctx.beginPath();
ctx.moveTo(x,z);
ctx.lineTo(x+r,z-2*r-1);
ctx.lineTo(x,z-2*r-1);
ctx.fill();

let grd2=ctx.createLinearGradient(x-r,z-r,x+r,z+r);
grd2.addColorStop(0,colour);

grd2.addColorStop(1,"#030303");
ctx.fillStyle=grd2;
ctx.beginPath();
ctx.arc(x,z-2*r,r,0,2*Math.PI);
ctx.fill();
ctx.fillStyle="#ffffff";
ctx.textAlign="center";
}

this.drawLine=function(startX,startZ,endX,endZ,colour) {
ctx.fillStyle="white";
ctx.strokeStyle="white";
if (colour)
ctx.strokeStyle=colour;
ctx.lineWidth=3;

ctx.beginPath();
ctx.moveTo(this.MCToDOM(startX),this.MCToDOM(startZ));
ctx.lineTo(this.MCToDOM(endX),this.MCToDOM(endZ));
ctx.stroke();
}

this.MCToDOM=function (_mc) {
return _mc/factor+1531;
}

this.DOMToMC=function (_dom) {
return (_dom-1531)*factor;
}

this.DOMPanTo=function (_x,_z) {
let x=_x/canvas.width*canvas.offsetWidth-$("#mapHolder")[0].offsetWidth/2;
let z=_z/canvas.height*canvas.offsetHeight-$("#mapHolder")[0].offsetHeight/2;

$("#mapHolder").animate({
scrollLeft: x+"px",
scrollTop: z+"px"
},500);
}

this.handleClick=function (_x,_y) {
let box=this.findClickbox(_x,_y);
if (box == null)
return;

this.onItemClick(box.point);

if (this.zoomPercentage <= 1.5)
this.zoom(2);

this.DOMPanTo(_x,_y);
}

this.onItemClick=function() {};

this.panToItem=function(_point) {
if (this.zoomPercentage <= 1.5)
this.zoom(2);
this.DOMPanTo(this.MCToDOM(_point.coords.x),this.MCToDOM(_point.coords.z));
}

this.findClickbox=function (_x,_y) {
for (c in clickboxes) {
if (_x < clickboxes[c].x || _y < clickboxes[c].y || _x > clickboxes[c].rx || _y > clickboxes[c].ry)
continue;

return clickboxes[c];
break;
}

return null;
}

this.zoomPercentage=1;
this.zoom=function (_percentage=1) {
const screenWidth=document.body.offsetWidth;
const screenHeight=document.body.offsetHeight;
const startMapSize=$("#mapCanvas")[0].offsetWidth;

let viewPosX=mapHolder.scrollLeft+(screenWidth/2);
let viewPosY=mapHolder.scrollTop+(screenHeight/2);
let percViewPosXMap=viewPosX/startMapSize;
let percViewPosYMap=viewPosY/startMapSize;

let endMapSize=startMapSize/this.zoomPercentage*_percentage;

$("#mapHolder").animate({
scrollLeft: percViewPosXMap*endMapSize-(screenWidth/2)+"px",
scrollTop: percViewPosYMap*endMapSize-(screenHeight/2)+"px"
},this.settings.animationSpeed);

$("#backgroundImage").animate({
width: parseFloat(_percentage)*100+"vw"
},this.settings.animationSpeed);
$("#mapCanvas")[0].style.width=parseFloat(_percentage)*100+"vw";
$("#mapCanvas")[0].style.height=parseFloat(_percentage)*100+"vw";

this.zoomPercentage=parseFloat(_percentage);
}

this.zoomIn=function () {
if (this.zoomPercentage+this.settings.zoomStepSize > this.settings.maxZoom)
return this.zoom(this.settings.maxZoom);
this.zoom(this.zoomPercentage+this.settings.zoomStepSize);
}

this.zoomOut=function () {
if (this.zoomPercentage-this.settings.zoomStepSize < 1)
return this.zoom(1);
this.zoom(this.zoomPercentage-this.settings.zoomStepSize);
}
};
function _server() {
this.items=[];

 this.getData=function(_filePath) {
return new Promise(function (resolve,fail) {
REQUEST.send(_filePath,"").then(function (_data) {
if (typeof _data != "object") return console.error(_filePath+": there's a problem in your json syntax");
Server.items=_data;
resolve(_data);
},function (_e) {
console.error("An error accured while trying to get "+_filePath,_e);
});
});
}


this.getItemByTitle=function(_title) {
_title=_title.toLowerCase();
for (let i=0; i < this.items.length; i++)
{
if (this.items[i].title.toLowerCase() == _title) return this.items[i];
}
return false;
}
 
}