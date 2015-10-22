function resizeImage(){
    var height;

    if (typeof(window.innerWidth) === 'number') {
        height = window.innerHeight - 260;
    } else if (document.documentElement && document.documentElement.clientHeight) {
        height = document.documentElement.clientHeight;
    }

    document.getElementById("picView").style.maxHeight = height + "px";
}

window.onload = function() {
    resizeImage();
};

window.onresize = function() {
    resizeImage();
};