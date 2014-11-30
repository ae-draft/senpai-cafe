window.attachEvent('onload', mkwidth);
window.attachEvent('onresize', mkwidth);

var minwidth_header = document.getElementById("header").currentStyle['min-width'].replace('px', '');
var minwidth_body = document.getElementById("body").currentStyle['min-width'].replace('px', '');

function mkwidth()
{
    document.getElementById("header").style.width = document.documentElement.clientWidth < minwidth_header ? minwidth_header+"px" : "";
    document.getElementById("body").style.width = document.documentElement.clientWidth < minwidth_body ? minwidth_header+"px" : "";
}