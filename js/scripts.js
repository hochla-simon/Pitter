function height(bloc){
    var hauteur;
    
    if( typeof( window.innerWidth ) == 'number' )
		hauteur = window.innerHeight - 30;
    else if( document.documentElement && document.documentElement.clientHeight )
        hauteur = document.documentElement.clientHeight;
    
    document.getElementById(bloc).style.height = hauteur+"px";
}

window.onload = function(){ 
	if(window.innerWidth>500){
		height("content-box1");
	}
	height("content-box2");
};
window.onresize = function(){
	if(window.innerWidth>500){
		height("content-box1");
	}
	height("content-box2");};
