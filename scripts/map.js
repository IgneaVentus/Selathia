
$(document).ready(function() {
    console.log("Inicjalizacja skryptu mapy");
    var img=document.getElementById('map');
    console.log(img.clientWidth+" "+img.clientHeight);
    const natSize = [1024/img.clientWidth,768/img.clientHeight];
    var offset = $("#map").offset();
    $("#map").click(function(e) {
        var coords = [(rounder((e.clientX*natSize[0]-offset.left),2)), (rounder((e.clientY*natSize[1]-offset.top),2))];
        $("#coordX").text(coords[0]);
        $("#coordY").text(coords[1]);
        updatemark(e.clientX, e.clientY);
    });
});

function updatemark(x,y) {
    $("#pointer").offset({top: (y-10), left: (x-10)}).show();

}

function rounder(x,r){
    if (r>0) {
        r*=10;
        x*=r;
        x=Math.round(x);
        x/=r;
        return x;
    }
    else {
        console.log("Error: Wrong number of numbers after period."+x+" "+r);
    }
}